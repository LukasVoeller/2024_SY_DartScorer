<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameLeg;
use App\Entity\GameScore;
use App\Entity\GameSet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GameTypeX01;
use App\Entity\Player;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/game/{id}', name: 'game')]
    public function index(Request $request, int $id): Response
    {
        $game = $this->entityManager->getRepository(GameTypeX01::class)->find($id);

        if ($game) {
            if ($game->getState() == "Live") {
                return $this->render('game/index.html.twig', [
                    'game' => $game,
                ]);
            } elseif ($game->getState() == "Finished") {
                return $this->render('game/finished.html.twig', [
                    'game' => $game,
                ]);
            }
        }

        return $this->render('game/not-found.html.twig', [
            'gameId' => $id,
        ]);
    }

    #[Route('/api/game/create', name: 'api_create_game', methods: ['POST'])]
    public function createGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $this->createNewGameX01($data);

        return $this->json(['gameId' => $gameId]);
    }

    #[Route('/api/game/save', name: 'api_save_game', methods: ['POST'])]
    public function saveGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $success = $this->saveGameX01($data);

        return $this->json(['success' => $success]);
    }

    #[Route('/api/game/{id}', name: 'api_get_game', methods: ['GET'])]
    public function getGame(int $id): JsonResponse
    {
        $game = $this->entityManager->getRepository(Game::class)->find($id);

        $serializedGame = $this->serializer->serialize($game, 'json', ['groups' => ['game', 'set', 'leg', 'score']]);

        return new JsonResponse($serializedGame, 200, [], true);
    }

    private function createNewGameX01($data): int
    {
        $game = new GameTypeX01();
        $player1 = $this->entityManager->getRepository(Player::class)->find($data['player1Id']);
        $player2 = $this->entityManager->getRepository(Player::class)->find($data['player2Id']);

        $game->setStartScore($data['startScore']);
        $game->setFinishType($data['finishType']);
        $game->setMatchMode($data['matchMode']);
        $game->setMatchModeSetsNeeded($data['matchModeSetsNeeded']);
        $game->setMatchModeLegsNeeded($data['matchModeLegsNeeded']);
        $game->setPlayer1Id($data['player1Id']);
        $game->setPlayer2Id($data['player2Id']);
        $game->setStartingPlayerId($data['playerStartingId']);
        $game->setState("Live");

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game->getId();
    }

    private function saveGameX01($data): bool
    {
        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        if (!$game) {
            return false;
        }

        // Assuming each set contains exactly 3 legs
        $setsNeeded = $game->getMatchModeSetsNeeded();
        $legsNeeded = $game->getMatchModeLegsNeeded();
        $legsPerSetPlayed = $data['legsPerSetPlayed'];

        if ($game->getMatchMode() == "FirstToSets") {
            for ($setIndex = 0; $setIndex < $setsNeeded; $setIndex++) {
                $set = new GameSet();
                $set->setRelatedGame($game);
                $set->setWinnerPlayerId($data['setWinnerPlayerIds'][$setIndex]);
                $set->setMatchModeLegsNeeded($legsNeeded);
                $this->entityManager->persist($set);

                $legsInThisSet = $legsPerSetPlayed[$setIndex];
                for ($legIndex = 0; $legIndex < $legsInThisSet; $legIndex++) {
                    $leg = new GameLeg();
                    $leg->setRelatedSet($set);
                    $globalLegIndex = array_sum(array_slice($legsPerSetPlayed, 0, $setIndex)) + $legIndex;
                    $leg->setWinnerPlayerId($data['legWinnerPlayerIds'][$globalLegIndex]);
                    $this->entityManager->persist($leg);

                    // Handle scores for Player 1
                    $this->createPlayerScores(
                        $leg,
                        $data['player1Id'],
                        $data['player1TotalScores'][$globalLegIndex],
                        $data['player1DartsThrown'][$globalLegIndex] ?? []
                    );

                    // Handle scores for Player 2
                    $this->createPlayerScores(
                        $leg,
                        $data['player2Id'],
                        $data['player2TotalScores'][$globalLegIndex],
                        $data['player2DartsThrown'][$globalLegIndex] ?? []
                    );
                }
            }
        } else if ($game->getMatchMode() == "FirstToLegs") {
            $numLegs = count($data['legWinnerPlayerIds']);

            for ($legIndex = 0; $legIndex < $numLegs; $legIndex++) {
                $leg = new GameLeg();
                $leg->setRelatedGame($game);
                $leg->setWinnerPlayerId($data['legWinnerPlayerIds'][$legIndex]);
                $this->entityManager->persist($leg);

                // Handle scores for Player 1
                $this->createPlayerScores(
                    $leg,
                    $data['player1Id'],
                    $data['player1TotalScores'][$legIndex],
                    $data['player1DartsThrown'][$legIndex] ?? []
                );

                // Handle scores for Player 2
                $this->createPlayerScores(
                    $leg,
                    $data['player2Id'],
                    $data['player2TotalScores'][$legIndex],
                    $data['player2DartsThrown'][$legIndex] ?? []
                );
            }
        }

        $game->setState($data['gameState']);
        $game->setWinnerPlayerId($data['gameWinnerPlayerId']);

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return true;
    }

    private function createPlayerScores(GameLeg $leg, int $playerId, array $scores, array $dartsThrown): void
    {
        foreach ($scores as $scoreIndex => $score) {
            $darts = $dartsThrown[$scoreIndex] ?? 0; // Default to 0 if no darts data
            $this->createScore($leg, $playerId, $score, $darts);
        }
    }

    private function createScore(GameLeg $leg, int $playerId, int $score, int $dartsThrown): void
    {
        $gameScore = new GameScore();
        $gameScore->setRelatedLeg($leg);
        $gameScore->setPlayerId($playerId);
        $gameScore->setValue($score);
        $gameScore->setDartsThrown($dartsThrown);

        $this->entityManager->persist($gameScore);
    }
}

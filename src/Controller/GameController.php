<?php declare(strict_types=1);

/*
 * The Software is licensed, not sold. You may use the Software only as
 * permitted under the terms of the license agreement. Unauthorized use,
 * modification, distribution, or copying of the Software is strictly prohibited.
 *
 * @copyright  Copyright (c) Lukas Völler.
 * @author     Lukas Völler
 * @license    Proprietary License
 * @link       https://www.vllr.lu
 */

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameLeg;
use App\Entity\GameScore;
use App\Entity\GameSet;
use App\Entity\GameTypeX01;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
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

    #[Route('/api/game', name: 'api_get_game', methods: ['GET'])]
    public function getGames(Request $request): JsonResponse
    {
        $games = $this->entityManager->getRepository(Game::class)->findAll();
        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['game', 'set', 'leg', 'score']]);
        return new JsonResponse($serializedGames, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/latest-five', name: 'api_get_game_latest_five', methods: ['GET'])]
    public function getGamesLatestFive(Request $request): JsonResponse
    {
        $games = $this->entityManager->getRepository(Game::class)->findLatestFiveGames();
        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['game', 'set', 'leg', 'score']]);
        return new JsonResponse($serializedGames, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/id/{id}', name: 'api_get_game_by_id', methods: ['GET'])]
    public function getGameById(int $id): JsonResponse
    {
        $game = $this->entityManager->getRepository(Game::class)->find($id);
        $serializedGame = $this->serializer->serialize($game, 'json', ['groups' => ['game', 'set', 'leg', 'score']]);
        return new JsonResponse($serializedGame, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/save', name: 'api_save_game', methods: ['POST'])]
    public function saveGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $success = $this->saveGameX01($data);
        return $this->json(['success' => $success]);
    }

    // TODO: Rename api_to_throw_game to api_game_to-throw
    #[Route('/api/game/to-throw', name: 'api_to_throw_game', methods: ['POST'])]
    public function setToThrow(Request $request, HubInterface $hub): Response
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $data['gameId'];
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        $toThrowPlayerId = $game->getToThrowPlayerId();
        $player1Id = $game->getPlayer1Id();
        $player2Id = $game->getPlayer2Id();

        if ($toThrowPlayerId == $player1Id) {
            $newToThrowPlayerId = $player2Id;
            $game->setToThrowPlayerId($newToThrowPlayerId);
        } elseif ($toThrowPlayerId == $player2Id) {
            $newToThrowPlayerId = $player1Id;
            $game->setToThrowPlayerId($newToThrowPlayerId);
        }

        $this->entityManager->flush();

        $updateUrl = 'https://vllr.lu/game/' . $gameId;
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => 'throw',
                'toThrowPlayerId' => $newToThrowPlayerId,
            ])
        );

        $hub->publish($update);

        return $this->json(['success' => true]);
    }

    private function saveGameX01($data): bool
    {
        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        if (!$game) {
            return false;
        }

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

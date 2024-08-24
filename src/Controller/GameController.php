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
use App\Entity\GameTally;
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

    #[Route('/game/{id}', name: 'app_game', requirements: ['id' => '\d+|new'])]
    public function index(Request $request, $id): Response
    {
        if ($id === 'new') {
            return $this->render('game_new/index.html.twig');
        }

        $id = (int) $id;
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
//        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['game', 'set', 'leg', 'score', 'api_player']]);
        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['api_game_plain', 'api_player_plain']]);
        return new JsonResponse($serializedGames, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/latest-three', name: 'api_get_game_latest_five', methods: ['GET'])]
    public function getGamesLatestFive(Request $request): JsonResponse
    {
        $games = $this->entityManager->getRepository(Game::class)->findLatestThreeGames();
//        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['game', 'set', 'leg', 'score', 'api_player']]);
        $serializedGames = $this->serializer->serialize($games, 'json', ['groups' => ['api_game_plain', 'api_player_plain']]);
        return new JsonResponse($serializedGames, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/id/{id}', name: 'api_get_game_by_id', methods: ['GET'])]
    public function getGameById(int $id): JsonResponse
    {
        $game = $this->entityManager->getRepository(Game::class)->find($id);
//        $serializedGame = $this->serializer->serialize($game, 'json', ['groups' => ['game', 'set', 'leg', 'score', 'api_player']]);
        $serializedGame = $this->serializer->serialize($game, 'json', ['groups' => ['api_game_full', 'api_player_plain']]);
        return new JsonResponse($serializedGame, Response::HTTP_OK, [], true);
    }

    #[Route('/api/game/save', name: 'api_save_game', methods: ['POST'])]
    public function saveGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $success = $this->saveGameX01($data);
        return $this->json(['success' => $success]);
    }

    #[Route('/api/game/id/{id}', name: 'api_game_update_state', methods: ['PUT'])]
    public function updateGameShot(int $id, Request $request, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $game = $this->entityManager->getRepository(Game::class)->find($id);

        $gameState = $data['gameState'];
        $winnerPlayerId = $data['winnerPlayerId'];

        $game->setState($gameState);
        $game->setWinnerPlayerId($winnerPlayerId);

        // Persist and flush the changes
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $gameId = $game->getId();
        //$this->sendUpdate($gameId, 'finished', $hub);

        // Return a JSON response indicating success
        return new JsonResponse(['success' => true, 'message' => 'Game state updated successfully.'], Response::HTTP_OK);
    }

    function sendUpdate($gameId, $type, $hub) {
        $updateUrl = 'https://vllr.lu/game/' . $gameId;
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => $type,
            ])
        );
        $hub->publish($update);
    }
}

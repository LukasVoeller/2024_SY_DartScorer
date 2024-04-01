<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GameX01;
use App\Entity\Player;
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
        $game = $this->entityManager->getRepository(GameX01::class)->find($id);

        //$serializedGame = $this->serializer->serialize($game, 'json', ['groups' => 'api_game']);

        /*
        return new Response($serializedGame, 200, [
            'Content-Type' => 'application/json'
        ]);
        */

        return $this->render('game/index.html.twig', [
            'game' => $game,
        ]);

    }

    #[Route('/api/game', name: 'api_post_game', methods: ['POST'])]
    public function postGame(Request $request): Response
    {
        // Handle POST request to add a new player
        $data = json_decode($request->getContent(), true);

        $game = null;
        $gameMode = $data['gameMode'];

        if ($gameMode == "X01") {
            $startScore = $data['startScore'];
            $finishType = $data['finishType'];
        }

        $matchMode = $data['matchMode'];
        $modeSets = $data['matchModeSets'];
        $modeLegs = $data['matchModeLegs'];
        $player1Id = $data['player1Id'];
        $player2Id = $data['player2Id'];
        $playerStartingId = $data['playerStartingId'];

        // Find players by their IDs
        $playerRepository = $this->entityManager->getRepository(Player::class);
        $player1 = $playerRepository->find($player1Id);
        $player2 = $playerRepository->find($player2Id);

        // TODO: Dont reference as Player Entity
        $playerStarting = $playerRepository->find($playerStartingId);

        // Perform any necessary game logic here
        // For example, you can create a new Game entity and associate players with it
        if ($gameMode == "X01") {
            $game = new GameX01();
            $game->setStartScore($startScore);
            $game->setFinishType($finishType);
        }

        $game->setMatchMode($matchMode);
        $game->setMatchModeSets($modeSets);
        $game->setMatchModeLegs($modeLegs);
        $game->setPlayer1($player1);
        $game->setPlayer2($player2);
        $game->setPlayerStarting($playerStarting);
        $game->setState("Live");

        // Persist the Game entity
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // Set the game code as the id of the game
        $gameCode = $game->getId();

        // Return the game code as JSON response
        return $this->json(['gameCode' => $gameCode]);
    }

    #[Route('/api/game/{id}', name: 'api_get_game', methods: ['GET'])]
    public function getGame(int $id): JsonResponse
    {
        $game = $this->entityManager->getRepository(GameX01::class)->find($id);
        //return $this->json($game);

        $serializedGame = $this->serializer->serialize($game, 'json', ['groups' => 'api_game']);

        return new JsonResponse($serializedGame, 200, [], true);
    }
}

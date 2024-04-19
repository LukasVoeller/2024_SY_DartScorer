<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Leg;
use App\Entity\Score;
use App\Entity\Set;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\GameX01;
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
        $data = json_decode($request->getContent(), true);

        $gameId = $this->createNewGameX01($data);

        return $this->json(['gameId' => $gameId]);
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
        $game = new GameX01();
        $player1 = $this->entityManager->getRepository(Player::class)->find($data['player1Id']);
        $player2 = $this->entityManager->getRepository(Player::class)->find($data['player2Id']);

        $game->setStartScore($data['startScore']);
        $game->setFinishType($data['finishType']);
        $game->setMatchMode($data['matchMode']);
        $game->setMatchModeSetsNeeded($data['matchModeSetsNeeded']);
        $game->setMatchModeLegsNeeded($data['matchModeLegsNeeded']);
        $game->setPlayer1Id($data['player1Id']);
        $game->setPlayer1Name($player1->getName());
        $game->setPlayer2Id($data['player2Id']);
        $game->setPlayer2Name($player2->getName());
        $game->setPlayerStartingId($data['playerStartingId']);
        $game->setState("Live");

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game->getId();
    }
}

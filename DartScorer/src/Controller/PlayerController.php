<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/player', name: 'player')]
    public function index(): Response
    {
        return $this->render('player/index.html.twig');
    }

    #[Route('/api/players', name: 'api_get_players', methods: ['GET'])]
    public function getPlayers(Request $request): JsonResponse
    {
        //$players = $this->entityManager->getRepository(Player::class)->findAll();
        //return $this->json($players);

        $players = $this->entityManager->getRepository(Player::class)->findAll();

        // Serialize players with 'api' serialization group
        $data = $this->serializer->serialize($players, 'json', ['groups' => 'api_player']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/player/{id}', name: 'api_get_player', methods: ['GET'])]
    public function getPlayer(int $id): JsonResponse
    {
        //$player = $this->entityManager->getRepository(Player::class)->find($id);
        //return $this->json($player);

        $player = $this->entityManager->getRepository(Player::class)->find($id);

        // Serialize player with 'api' serialization group
        $data = $this->serializer->serialize($player, 'json', ['groups' => 'api_player']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/player', name: 'api_post_player', methods: ['POST'])]
    public function postPlayer(Request $request): JsonResponse
    {
        // Handle POST request to add a new player
        $data = json_decode($request->getContent(), true);
        $player = new Player();
        $player->setName($data['name']); // Assuming 'name' is the key in the JSON data

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        //return $this->json($player);

        // Serialize player with 'api' serialization group
        $data = $this->serializer->serialize($player, 'json', ['groups' => 'api_player']);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/player/{id}', name: 'api_delete_player', methods: ['DELETE'])]
    public function deletePlayer(int $id): JsonResponse
    {
        $player = $this->entityManager->getRepository(Player::class)->find($id);

        if (!$player) {
            // Player not found, return a 404 response
            return new JsonResponse(['message' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        // Remove the player from the database
        $this->entityManager->remove($player);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Player deleted successfully']);
    }
}

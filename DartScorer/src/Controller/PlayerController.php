<?php

namespace App\Controller;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PlayerController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/player', name: 'player')]
    public function index(): Response
    {
        return $this->render('player/index.html.twig');
    }

    #[Route('/api/players', name: 'api_get_players', methods: ['GET'])]
    public function getPlayers(Request $request): JsonResponse
    {
        // Handle GET request to fetch all players
        $players = $this->entityManager->getRepository(Player::class)->findAll();

        return $this->json($players);
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

        return $this->json($player);
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

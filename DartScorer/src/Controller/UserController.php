<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->serializer = $serializer;
    }

    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/api/user', name: 'api_get_user', methods: ['GET'])]
    public function getUsers(Request $request): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $data = $this->serializer->serialize($users, 'json', ['groups' => 'api_user']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/user/{id}', name: 'api_get_user', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'api_user']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/user', name: 'api_post_user', methods: ['POST'])]
    public function postUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Create a new User entity
        $user = new User();
        $user->setUsername($data['username']);
        $encodedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);
        $user->setRoles([$data['role']]);

        // Fetch player
        $playerId = $data['player'];
        $playerRepository = $this->entityManager->getRepository(Player::class);
        $player = $playerRepository->find($playerId);
        if ($player) {
            $user->setPlayer($player);
        } else {
            $user->setPlayer(null);
            //return new JsonResponse(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        //$player->setUser($user);

        // Persist and flush the User entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        //return $this->json($user);

        // Serialize user with 'api' serialization group
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'api_user']);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/user/{id}', name: 'api_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            // User not found, return a 404 response
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Remove the player from the database
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully']);
    }
}

<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameLeg;
use App\Entity\GameSet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SetController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/set/create', name: 'api_set_create')]
    public function createSet(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $data['gameId'];
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);
        $matchModeLegsNeeded = $game->getMatchModeLegsNeeded();

        $set = new GameSet();
        $set->setRelatedGame($game);
        $set->setMatchModeLegsNeeded($matchModeLegsNeeded);

        $this->entityManager->persist($set);
        $this->entityManager->flush();

        return $this->json(['setId' => $set->getId()]);
    }

    #[Route('/api/set/update', name: 'api_set_update')]
    public function updateSet(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $setId = $data['setId'];
        $winnerPlayerId = $data['winnerPlayerId'];
        $set = $this->entityManager->getRepository(GameSet::class)->find($setId);

        $set->setWinnerPlayerId($winnerPlayerId);

        $this->entityManager->flush();

        return $this->json($data);
    }
}

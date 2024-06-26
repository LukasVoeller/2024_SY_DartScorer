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

class LegController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/leg/create', name: 'api_leg_create')]
    public function createLeg(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $setId = $data['setId'];
        $gameId = $data['gameId'];
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        if ($setId) {
            $leg = new GameLeg();
            $set = $this->entityManager->getRepository(GameSet::class)->find($setId);
            $leg->setRelatedSet($set);
            $leg->setRelatedGame($game);
        } else {
            $leg = new GameLeg();
            $leg->setRelatedGame($game);
        }

        $this->entityManager->persist($leg);
        $this->entityManager->flush();

        return $this->json(['legId' => $leg->getId()]);
    }

    #[Route('/api/leg/update', name: 'api_leg_update')]
    public function updateLeg(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $legId = $data['legId'];
        $winnerPlayerId = $data['winnerPlayerId'];
        $leg = $this->entityManager->getRepository(GameLeg::class)->find($legId);

        $leg->setWinnerPlayerId($winnerPlayerId);

        $this->entityManager->flush();

        return $this->json($data);
    }
}

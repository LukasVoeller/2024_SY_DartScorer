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

use App\Entity\GameLeg;
use App\Entity\GameSet;
use App\Entity\GameTally;
use App\Entity\GameTypeX01;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewGameController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/new-game', name: 'new-game')]
    public function index(): Response
    {
        return $this->render('new_game/index.html.twig');
    }

    #[Route('/api/game/create', name: 'api_game_create', methods: ['POST'])]
    public function createGame(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $this->createNewGameX01($data);
        return $this->json(['gameId' => $gameId]);
    }

    private function createNewGameX01($data): int
    {
        $game = new GameTypeX01();
        $tallyPlayer1 = new GameTally();
        $tallyPlayer2 = new GameTally();

        $game->setStartScore($data['startScore']);
        $game->setFinishType($data['finishType']);
        $game->setMatchMode($data['matchMode']);
        $game->setMatchModeSetsNeeded($data['matchModeSetsNeeded']);
        $game->setMatchModeLegsNeeded($data['matchModeLegsNeeded']);
        $game->setPlayer1Id($data['player1Id']);
        $game->setPlayer2Id($data['player2Id']);
        //$game->setStartingPlayerId($data['playerStartingId']);
        //$game->setToThrowPlayerId($data['playerStartingId']);
        $game->setState("Live");
        $game->setType("Match");

        //dump($game);

        $tallyPlayer1->setScore($data['startScore']);
        $tallyPlayer2->setScore($data['startScore']);
        $tallyPlayer1->setPlayerId($data['player1Id']);
        $tallyPlayer2->setPlayerId($data['player2Id']);

        if ($data['playerStartingId'] == $data['player1Id']) {
            $tallyPlayer1->setStartedLeg(true);
            $tallyPlayer1->setToThrow(true);
        } elseif ($data['playerStartingId'] == $data['player2Id']) {
            $tallyPlayer2->setStartedLeg(true);
            $tallyPlayer2->setToThrow(true);
        }

        if ($game->getMatchMode() == "FirstToLegs") {
            $leg = new GameLeg();
            $leg->setRelatedGame($game);
            $this->entityManager->persist($leg);
        } elseif ($game->getMatchMode() == "FirstToSets") {
            $set = new GameSet();
            $set->setRelatedGame($game);
            $set->setMatchModeLegsNeeded($game->getMatchModeLegsNeeded());
            $leg = new GameLeg();
            $leg->setRelatedSet($set);
            $this->entityManager->persist($set);
            $this->entityManager->persist($leg);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        if ($game->getMatchMode() == "FirstToLegs") {
            $tallyPlayer1->setLegId($leg->getId());
            $tallyPlayer2->setLegId($leg->getId());
        } elseif ($game->getMatchMode() == "FirstToSets") {
            $tallyPlayer1->setLegId($leg->getId());
            $tallyPlayer2->setLegId($leg->getId());
            $tallyPlayer1->setSetId($set->getId());
            $tallyPlayer2->setSetId($set->getId());
        }
        $tallyPlayer1->setGame($game);
        $tallyPlayer2->setGame($game);

        $this->entityManager->persist($tallyPlayer1);
        $this->entityManager->persist($tallyPlayer2);
        $this->entityManager->flush();

        return $game->getId();
    }
}

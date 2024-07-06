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
use App\Entity\GameTally;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TallyController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    // TODO: Rename to /api/tally
    #[Route('/api/tally', name: 'api_tally')]
    public function getTally(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $data['gameId'];
        $playerId = $data['playerId'];

        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $serializedTally = $this->serializer->serialize($tally, 'json', ['groups' => ['tally']]);
        return new JsonResponse($serializedTally, Response::HTTP_OK, [], true);
    }

    #[Route('/api/tally/update-score', name: 'api_tally_update_score')]
    public function updateTallyScore(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $data['gameId'];
        $playerId = $data['playerId'];
        $score = $data['score'];
        $legsWon = $data['legsWon'];
        $setsWon = $data['setsWon'];

        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $tally->setScore($score);
        $tally->setLegsWon($legsWon);
        $tally->setSetsWon($setsWon);

        $this->entityManager->flush();

        return $this->json($data);
    }

    #[Route('/api/tally/update-leg-set', name: 'api_tally_update_leg_set')]
    public function updateTallyLegSet(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $data['gameId'];
        $playerId = $data['playerId'];
        $legId = $data['legId'];
        $setID = $data['setId'];

        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);

        if ($legId !== null) {
            $tally->setLegId($legId);
        }

        if ($setID !== null) {
            $tally->setSetId($setID);
        }

        $this->entityManager->flush();

        return $this->json($data);
    }

    #[Route('/api/tally/switch-to-throw', name: 'api_tally_switch_to_throw', methods: ['POST'])]
    public function switchToThrow(Request $request, HubInterface $hub): Response
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $data['gameId'];
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        $player1Id = $game->getPlayer1Id();
        $player2Id = $game->getPlayer2Id();

        $tallyPlayer1 = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $player1Id);
        $tallyPlayer2 = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $player2Id);

        if ($tallyPlayer1->getToThrow()) {
            $tallyPlayer1->setToThrow(false);
            $tallyPlayer2->setToThrow(true);
        } elseif ($tallyPlayer2->getToThrow()) {
            $tallyPlayer2->setToThrow(false);
            $tallyPlayer1->setToThrow(true);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/api/tally/switch-to-start-leg', name: 'api_tally_switch_to_start_leg', methods: ['POST'])]
    public function switchToStartLeg(Request $request, HubInterface $hub): Response
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $data['gameId'];
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        $player1Id = $game->getPlayer1Id();
        $player2Id = $game->getPlayer2Id();

        $tallyPlayer1 = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $player1Id);
        $tallyPlayer2 = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $player2Id);

        if ($tallyPlayer1->getStartedLeg()) {
            $tallyPlayer1->setStartedLeg(false);
            $tallyPlayer2->setStartedLeg(true);
        } elseif ($tallyPlayer2->getStartedLeg()) {
            $tallyPlayer2->setStartedLeg(false);
            $tallyPlayer1->setStartedLeg(true);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }
}

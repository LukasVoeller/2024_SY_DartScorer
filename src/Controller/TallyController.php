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
    #[Route('/api/game/id/{gameId}/player/id/{playerId}', name: 'api_get_tally', methods: ['GET'])]
    public function getTally(int $gameId, int $playerId): JsonResponse
    {
        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $serializedTally = $this->serializer->serialize($tally, 'json', ['groups' => ['tally']]);
        return new JsonResponse($serializedTally, Response::HTTP_OK, [], true);
    }

    #[Route('/api/tally/update', name: 'api_tally_update')]
    public function updateTally(Request $request): Response
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

        $this->entityManager->persist($tally);
        $this->entityManager->flush();

        return $this->json($data);
    }
}

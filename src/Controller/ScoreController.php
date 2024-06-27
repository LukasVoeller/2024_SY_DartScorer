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
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ScoreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/api/score/confirm', name: 'api_confirm_score')]
    public function confirmScore(Request $request, HubInterface $hub): Response
    {
        $data = json_decode($request->getContent(), true);

        $gameId = $data['gameId'];
        $playerId = $data['playerId'];
        $thrownScore = $data['thrownScore'];
        $thrownDarts = $data['thrownDarts'];
        $switchToTrow = $data['switchToTrow'];
        $isCheckout = $data['isCheckout'];

        $game = $this->entityManager->getRepository(Game::class)->find($gameId);
        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $legId = $tally->getLegId();
        $leg = $this->entityManager->getRepository(GameLeg::class)->find($legId);

        $score = new GameScore();
        $score->setRelatedLeg($leg);
        $score->setPlayerId($playerId);
        $score->setValue($thrownScore);
        $score->setDartsThrown($thrownDarts);
        $score->setCheckout($isCheckout);

        $currentScore = $tally->getScore();
        if ($currentScore - $thrownScore >= 2){
            $newTotalScore = $currentScore - $thrownScore;
            $tally->setScore($newTotalScore);
            $this->sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, $switchToTrow, 'confirm', $hub);
        } elseif ($currentScore - $thrownScore == 0){
            $newTotalScore = $currentScore - $thrownScore;
            $tally->setScore($newTotalScore);
            $this->sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, $switchToTrow, 'checkout', $hub);
        }

        $this->entityManager->persist($score);
        $this->entityManager->flush();
        return $this->json($data);
    }

    function sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, $switchToTrow, $type, $hub) {
        $updateUrl = 'https://vllr.lu/game/' . $gameId;
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => $type,
                'playerId' => $playerId,
                'thrownScore' => $thrownScore,
                'newTotalScore' => $newTotalScore,
                'switchToTrow' => $switchToTrow,
            ])
        );
        $hub->publish($update);
    }

    #[Route('/api/score/last', name: 'api_last_scores')]
    public function lastScores(Request $request, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        //$game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);

        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($data['gameId'], $data['playerId']);
        $currentLegId = $tally->getLegId();

        $scores = $scoreRepository->findLastScoresByPlayerIdAndLegId($data['playerId'], $currentLegId);
        $serializedScores = $this->serializer->serialize($scores, 'json', ['groups' => ['score']]);

        return new JsonResponse($serializedScores, Response::HTTP_OK, [], true);
    }

    #[Route('/api/score/undo', name: 'api_undo_score')]
    public function undoScore(Request $request, HubInterface $hub, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $gameId = $data['gameId'];
        $playerId = $data['playerId'];
        $switchToThrow = $data['switchToThrow'];

        //$game = $this->entityManager->getRepository(Game::class)->find($gameId );
        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $legId = $tally->getLegId();
        $lastScore = $scoreRepository->findLatestScoreByPlayerIdAndLegId($playerId, $legId);

        $newTotalScore = $tally->getScore() + $lastScore->getValue();
        $tally->setScore($newTotalScore);

        if (!$lastScore) {
            return new JsonResponse(['error' => 'No score found to delete.'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($lastScore);

        $this->entityManager->flush();

        $updateUrl = 'https://vllr.lu/game/' . $data['gameId'];
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => 'undo',
                'playerId' => $playerId,
                'newTotalScore' => $newTotalScore,
                'switchToThrow' => $switchToThrow,
            ])
        );
        $hub->publish($update);

        return $this->json($data);
    }

    /*
    #[Route('/api/score/delete-latest', name: 'api_delete_latest_score')]
    public function deleteLatestScore(Request $request, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $latestScore = $scoreRepository->findLatestByPlayerIdAndLegId($data['playerId'], $data['legId']);

        if (!$latestScore) {
            return new JsonResponse(['error' => 'No score found to delete.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($latestScore);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Score deleted successfully.'], Response::HTTP_OK);
    }
    */
}

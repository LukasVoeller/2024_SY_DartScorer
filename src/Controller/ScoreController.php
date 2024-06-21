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

        $game = $this->entityManager->getRepository(Game::class)->find($gameId);
        $tally = $this->entityManager->getRepository(GameTally::class)->findByGameIdAndPlayerId($gameId, $playerId);
        $legId = $tally->getLegId();
        $leg = $this->entityManager->getRepository(GameLeg::class)->find($legId);

        $score = new GameScore();
        $score->setRelatedLeg($leg);
        $score->setPlayerId($playerId);
        $score->setValue($thrownScore);
        $score->setDartsThrown($thrownDarts);

        $currentScore = $tally->getScore();
        if ($currentScore - $thrownScore >= 2){                                              // Scoring
            $newTotalScore = $currentScore - $thrownScore;
            $tally->setScore($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer2Id());
            $this->sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, 'confirm', $hub);
        } elseif ($currentScore - $thrownScore == 0){                                        // Checkout
            $newTotalScore = $currentScore - $thrownScore;
            $tally->setScore($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer2Id());
            $this->sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, 'checkout', $hub);
        }

        $this->entityManager->persist($score);
        $this->entityManager->flush();
        return $this->json($data);
    }

    function sendUpdate($gameId, $playerId, $thrownScore, $newTotalScore, $type, $hub) {
        $updateUrl = 'https://vllr.lu/game/' . $gameId;
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => $type,
                'playerId' => $playerId,
                'thrownScore' => $thrownScore,
                'newTotalScore' => $newTotalScore,
            ])
        );
        $hub->publish($update);
    }

    #[Route('/api/score/last', name: 'api_last_scores')]
    public function lastScores(Request $request, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);

//        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);

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
        $playerId = $data['playerId'];
        $switchToThrow = $data['switchToThrow'];
        //$undoScore = $data['undoScore'] ?? null;

        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        $legId = $game->getCurrentLegId();
        $latestScore = $scoreRepository->findLatestScoreByPlayerIdAndLegId($playerId, $legId);

        if ($game->getPlayer1Id() == $data['playerId']) {
            $newTotalScore = $game->getCurrentScorePlayer1() + $latestScore->getValue();

            $game->setCurrentScorePlayer1($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer2Id());
        } elseif ($game->getPlayer2Id() == $data['playerId']) {
            $newTotalScore = $game->getCurrentScorePlayer2() + $latestScore->getValue();

            $game->setCurrentScorePlayer2($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer1Id());
        }

        if (!$latestScore) {
            return new JsonResponse(['error' => 'No score found to delete.'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($latestScore);

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

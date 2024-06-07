<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameLeg;
use App\Entity\GameScore;
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

        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        $leg = $this->entityManager->getRepository(GameLeg::class)->find($game->getCurrentLegId());
        $playerId = $data['playerId'] ?? null;
        $thrownScore = $data['thrownScore'];
        $thrownDarts = $data['thrownDarts'];

        $score = new GameScore();
        $score->setRelatedLeg($leg);
        $score->setPlayerId($playerId);
        $score->setValue($thrownScore);
        $score->setDartsThrown($thrownDarts);
        $this->entityManager->persist($score);

        if ($game->getPlayer1Id() == $data['playerId']) {
            $newTotalScore = $game->getPlayer1Score() - $thrownScore;

            $game->setPlayer1Score($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer2Id());
        } elseif ($game->getPlayer2Id() == $data['playerId']) {
            $newTotalScore = $game->getPlayer2Score() - $thrownScore;

            $game->setPlayer2Score($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer1Id());
        }

        $updateUrl = 'https://vllr.lu/game/' . $data['gameId'];
        $update = new Update(
            $updateUrl,
            json_encode([
                'eventType' => 'confirm',
                'playerId' => $playerId,
                'thrownScore' => $thrownScore,
                'newTotalScore' => $newTotalScore,
            ])
        );

        $this->entityManager->flush();
        $hub->publish($update);

        return $this->json($data);
    }

    #[Route('/api/score/last', name: 'api_last_scores')]
    public function lastScores(Request $request, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        $currentLeg = $game->getCurrentLegId();

        $scores = $scoreRepository->findLastScoresByPlayerIdAndLegId($data['playerId'], $currentLeg);
        $serializedScores = $this->serializer->serialize($scores, 'json', ['groups' => ['score']]);

        return new JsonResponse($serializedScores, Response::HTTP_OK, [], true);
    }

    #[Route('/api/score/undo', name: 'api_undo_score')]
    public function undoScore(Request $request, HubInterface $hub, ScoreRepository $scoreRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $playerId = $data['playerId'] ?? null;
        //$undoScore = $data['undoScore'] ?? null;

        $game = $this->entityManager->getRepository(Game::class)->find($data['gameId']);
        $legId = $game->getCurrentLegId();
        $latestScore = $scoreRepository->findLatestScoreByPlayerIdAndLegId($playerId, $legId);

        if ($game->getPlayer1Id() == $data['playerId']) {
            $newTotalScore = $game->getPlayer1Score() + $latestScore->getValue();

            $game->setPlayer1Score($newTotalScore);
            $game->setToThrowPlayerId($game->getPlayer2Id());
        } elseif ($game->getPlayer2Id() == $data['playerId']) {
            $newTotalScore = $game->getPlayer2Score() + $latestScore->getValue();

            $game->setPlayer2Score($newTotalScore);
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
                'newTotalScore' => $newTotalScore
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

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

use App\Repository\ScoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileController extends AbstractController
{
//    #[Route('/profile', name: 'app_profile')]
//    public function index(): Response
//    {
//        return $this->render('profile/index.html.twig', [
//            'controller_name' => 'ProfileController',
//        ]);
//    }

    #[Route('/profile', name: 'app_profile')]
    public function index(SerializerInterface $serializer, ScoreRepository $scoreRepository): Response
    {
        $user = $this->getUser();

        // Serialize the user object to JSON
        $userData = $serializer->serialize($user, 'json', ['groups' => ['api_user']]);

        // Get scores thrown by the player
        $scores = $user->getPlayer() ? $scoreRepository->findScoresByPlayerId($user->getPlayer()->getId()) : [];
        $serializedScores = $serializer->serialize($scores, 'json', ['groups' => ['api_score_plain']]);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'userData' => $userData,
            'totalGamesPlayed' => $user->getPlayer() ? $user->getPlayer()->getTotalGamesPlayed() : 0,
            'totalGamesWon' => $user->getPlayer() ? $user->getPlayer()->getTotalGamesWon() : 0,
            'totalGamesLost' => $user->getPlayer() ? $user->getPlayer()->getTotalGamesLost() : 0,
            'liveGamesCount' => $user->getPlayer() ? $user->getPlayer()->getLiveGamesCount() : 0,
            'finishedGamesCount' => $user->getPlayer() ? $user->getPlayer()->getFinishedGamesCount() : 0,
            'scores' => $serializedScores,
            'scoresRaw' => $scores,
        ]);
    }
}

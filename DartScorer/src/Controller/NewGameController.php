<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewGameController extends AbstractController
{
    #[Route('/new-game', name: 'new-game')]
    public function index(): Response
    {
        return $this->render('new_game/index.html.twig');
    }
}

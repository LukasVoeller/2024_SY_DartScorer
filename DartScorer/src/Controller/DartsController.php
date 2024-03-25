<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DartsController extends AbstractController
{
    #[Route('/darts', name: 'app_darts')]
    public function index(): Response
    {
        return $this->render('darts/index.html.twig', [
            'controller_name' => 'DartsController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MercureController extends AbstractController
{
    #[Route('/mercure', name: 'app_mercure')]
    public function index(): Response
    {
        return $this->render('mercure/index.html.twig', [
            'controller_name' => 'MercureController',
        ]);
    }
}

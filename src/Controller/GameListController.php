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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameListController extends AbstractController
{
    #[Route('/game', name: 'app_games')]
    public function index(): Response
    {
        return $this->render('game_list/index.html.twig');
    }
}

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

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'error' => $error, // pass the error to the template
        ]);
    }

    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login(Request $request, UserProviderInterface $userProvider, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // Validate the credentials (this is an example, actual validation may vary)
        $user = $userProvider->loadUserByUsername($username);
        if ($user instanceof UserInterface && $this->isPasswordValid($user, $password)) {
            $token = $jwtManager->create($user);
            return new JsonResponse(['token' => $token]);
        }

        return new JsonResponse(['error' => 'Invalid login credentials'], 401);
    }

    private function isPasswordValid(UserInterface $user, string $password): bool
    {
        return $this->get('security.password_encoder')->isPasswordValid($user, $password);
    }

}

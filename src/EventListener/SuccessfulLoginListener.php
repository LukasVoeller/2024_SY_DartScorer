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

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SuccessfulLoginListener
{
    private $sessionStack;

    public function __construct(RequestStack $sessionStack)
    {
        $this->sessionStack = $sessionStack;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the authenticated user
        $user = $event->getAuthenticationToken()->getUser();

        // Access and set the session using RequestStack
        $session = $this->sessionStack->getCurrentRequest()->getSession();
        // Set the target URL to the session
        $session->set('_security.main.target_path', '/darts');

        // You can perform additional actions here if needed
    }
}
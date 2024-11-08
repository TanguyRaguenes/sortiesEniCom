<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // Check if the user is already authenticated
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('info', 'The user is already logged in.');
            return $this->redirectToRoute('app_main');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $this->addFlash('success', 'The login was successful !');

        // Return the login page without the navigation bar
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'show_nav' => false,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->addFlash('success', 'The logout was successful !');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
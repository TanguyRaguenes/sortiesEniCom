<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{

    #[Route('/', name: 'app_main_home')]
    public function displayHome(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/welcome', name: 'app_main_welcome')]
    public function displayWelcome(): Response
    {

        $user = $this->getUser();

        return $this->render('main/welcome.html.twig', [
            'user' => $user,
        ]);
    }
    
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        // Pensez a forcer le log du user pour accédez au reste du site !!
        $user = $this->getUser();
        return $this->render('main/index.html.twig', [
            'user' => $user,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use App\Entity\User;
use App\Entity\Participant;
use App\Entity\Place;

use App\Form\PlaceFormType;


class PlaceController extends AbstractController
{
 #[Route('/place/create', name: 'app_place_create')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $form = $this->createForm(PlaceFormType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($place);
            $entityManager->flush();
            return $this->redirectToRoute('app_trip_create');

        }
        return $this->render('place/edit.html.twig', [
            'form' => $form,
        ]);
    }
}

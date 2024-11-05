<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TripController extends AbstractController
{
    #[Route('/trip/list', name: 'app_trip_list')]
    public function displayList(TripRepository $tripRepository): Response
    {

        $trips = $tripRepository->findAll();
        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
        ]);
    }
//    #[IsGranted('ROLE_ADMIN', 'ROLE_ORGANIZER')]
    #[Route('/trip/create', name: 'app_trip_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $trip=new Trip();

        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);

        if($form->isSubmitted()){

            $entityManager->persist($trip);
            $entityManager->flush();
            $this->addFlash('success', 'Trip successfully added !');
            return $this->redirectToRoute('app_trip_list');
        }
        return $this->render("trip/edit.html.twig",[

            'form'=>$form

        ]);

    }
}

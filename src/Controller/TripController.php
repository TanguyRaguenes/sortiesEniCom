<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/trip/list', name: 'app_trip_list')]
    public function displayList(TripRepository $tripRepository, CampusRepository $campusRepository, Request $request): Response
    {
        $campuses = $campusRepository->findAll();
        
        $selectedCampusId = $request->query->get('campus');

        if ($selectedCampusId) {
            $trips = $tripRepository->findBy(['campus' => $selectedCampusId]);
        } else {
            $trips = $tripRepository->findAll();
        }

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'campuses' => $campuses,
            'selectedCampusId' => $selectedCampusId,
        ]);
    }

    #[Route('/trip/create', name: 'app_trip_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, StateRepository $stateRepository): Response
    {

        $trip=new Trip();
        $user = $this->getUser();
        $organizer = $participantRepository->findOneByEmail($user->getEmail());
        $state = $stateRepository->findOneByLabel('Create');
        // dd($participant);

        $trip->setOrganizer($organizer);
        $trip->setState($state);


        $form = $this->createForm(TripFormType::class, $trip,[
            'organizer' => $organizer,
            'state' => $state,
        ]);
        $form->handleRequest($request);

        

        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($trip);
            $entityManager->flush();
            $this->addFlash('success', 'Trip successfully added !');
            return $this->redirectToRoute('app_trip_list');
        }

        return $this->render("trip/edit.html.twig",['form'=>$form]);

    }

    #[Route('/trip/detail/{id}', name: 'app_trip_detail', requirements: ['id' => '\d+'])]
    public function displayDetail(int $id, TripRepository $tripRepository): Response
    {
        $trip = $tripRepository->find($id);
        return $this->render('trip/detail.html.twig', [
            'trip' => $trip,
        ]);
    }
}

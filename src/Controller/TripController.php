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
    public function displayList(TripRepository $tripRepository, CampusRepository $campusRepository, ParticipantRepository $participantRepository, Request $request): Response
{
    $campuses = $campusRepository->findAll();
    $selectedCampusId = $request->query->get('campus');
    $filter = $request->query->get('filter');
    $user = $this->getUser();

    // Récupérer le Participant correspondant au User connecté
    $participant = $participantRepository->findOneByEmail($user->getEmail());

    $criteria = [];

    if ($selectedCampusId) {
        $criteria['campus'] = $selectedCampusId;
    }

    if ($filter === 'created' && $participant) {
        // Filtrer par organisateur
        $trips = $tripRepository->findTripsByOrganizer($participant, $criteria);
    } elseif ($filter === 'participate' && $participant) {
        // Filtrer par participant
        $trips = $tripRepository->findTripsByParticipant($participant, $criteria);
    } else {
        $trips = $tripRepository->findBy($criteria);
    }

    return $this->render('trip/list.html.twig', [
        'trips' => $trips,
        'campuses' => $campuses,
        'selectedCampusId' => $selectedCampusId,
        'selectedFilter' => $filter,
    ]);
}


    #[Route('/trip/create', name: 'app_trip_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, StateRepository $stateRepository): Response
    {
        $trip = new Trip();
        $user = $this->getUser();
        $organizer = $participantRepository->findOneByEmail($user->getEmail());
        $state = $stateRepository->findOneByLabel('Create');

        $trip->setOrganizer($organizer);
        $trip->setState($state);

        $form = $this->createForm(TripFormType::class, $trip, [
            'organizer' => $organizer,
            'state' => $state,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();
            $this->addFlash('success', 'Trip successfully added!');
            return $this->redirectToRoute('app_trip_list');
        }

        return $this->render("trip/edit.html.twig", ['form' => $form]);
    }

    #[Route('/trip/detail/{id}', name: 'app_trip_detail', requirements: ['id' => '\d+'])]
    public function displayDetail(int $id, TripRepository $tripRepository): Response
    {
        $trip = $tripRepository->find($id);
        return $this->render('trip/detail.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/trip/{id}/participate', name: 'app_trip_participate')]
    public function participate(int $id, TripRepository $tripRepository, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $trip = $tripRepository->find($id);
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            $user = $participantRepository->findOneByEmail($user->getEmail());
            if (!$user) {
                throw new \LogicException('User not found as a Participant.');
            }
        }

        if ($trip->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'You are already participating in this trip.');
            return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
        }

        $trip->addParticipant($user);
        $entityManager->persist($trip);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully registered for this trip.');
        return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
    }

    #[Route('/trip/{id}/remove', name: 'app_trip_participate_remove')]
    public function unparticipate(int $id, TripRepository $tripRepository, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $trip = $tripRepository->find($id);
        $user = $this->getUser();

        if (!$user instanceof Participant) {
            $user = $participantRepository->findOneByEmail($user->getEmail());
            if (!$user) {
                throw new \LogicException('User not found as a Participant.');
            }
        }

        if (!$trip->getParticipants()->contains($user)) {
            $this->addFlash('error', 'You are not participating in this trip.');
            return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
        }

        $trip->removeParticipant($user);
        $entityManager->persist($trip);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully unregistered from this trip.');
        return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
    }

    #[Route('/trip/delete/{id}', name: 'app_trip_delete')]
    public function delete(int $id, TripRepository $tripRepository, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository): Response
    {
        $trip = $tripRepository->find($id);

        if (!$trip) {
            throw $this->createNotFoundException('Trip not found');
        }

        $user = $this->getUser();
        $participant = $participantRepository->findOneByEmail($user->getEmail());
        if (!$participant) {
            throw $this->createAccessDeniedException('You must be a organizer to delete this trip');
        }

        if ($trip->getOrganizer() !== $participant) {
            throw $this->createAccessDeniedException('You do not have permission to delete this trip');
        }

        $entityManager->remove($trip);
        $entityManager->flush();

        $this->addFlash('success', 'Trip successfully deleted!');
        return $this->redirectToRoute('app_trip_list');
    }

    #[Route('/trip/edit/{id}', name: 'app_trip_edit')]
    public function update(int $id, Request $request, TripRepository $TripRepository, EntityManagerInterface $entityManager): Response
    {
        $trip = $TripRepository->find($id);
        if (!$trip) {
            throw $this->createNotFoundException('Trip not found');
        }
                 
        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);
                
                
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Trip updated !');
            return $this->redirectToRoute('app_trip_detail', ['id' => $trip->getId()]);
        }
        return $this->render("trip/edit.html.twig", [
            'form' => $form,
            'trip' => $trip,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PlaceRepository;
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
        $selectedDateOrder = $request->query->get('date_order', 'asc');
        $user = $this->getUser();

        $searchTerm = $request->query->get('search');

        $showPastTrips = $request->query->get('past_trips', null) !== null;
        $participant = $participantRepository->findOneByEmail($user->getEmail());

        $dateStart = $request->query->get('date_start');
        $dateEnd = $request->query->get('date_end');

        $qb = $tripRepository->createQueryBuilder('t');

        if ($searchTerm) {
            $qb->andWhere('LOWER(t.name) LIKE LOWER(:searchTerm)')
               ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }

        $now = new \DateTime();
        $thirtyDaysAgo = (clone $now)->modify('-30 days');

        if ($showPastTrips) {
            $qb->andWhere('t.dateAndTime < :thirtyDaysAgo')
               ->setParameter('thirtyDaysAgo', $thirtyDaysAgo);
        } else {
            $qb->andWhere('t.dateAndTime >= :thirtyDaysAgo')
               ->setParameter('thirtyDaysAgo', $thirtyDaysAgo);
        }

        if ($dateStart) {
            $startDate = new \DateTime($dateStart);
            $qb->andWhere('t.dateAndTime >= :startDate')
               ->setParameter('startDate', $startDate->setTime(0, 0, 0));
        }
    
        if ($dateEnd) {
            $endDate = new \DateTime($dateEnd);
            $qb->andWhere('t.dateAndTime <= :endDate')
               ->setParameter('endDate', $endDate->setTime(23, 59, 59));
        }

        if ($selectedCampusId) {
            $qb->andWhere('t.campus = :campusId')
               ->setParameter('campusId', $selectedCampusId);
        }

        if ($filter === 'created' && $participant) {
            $qb->andWhere('t.organizer = :participant')
               ->setParameter('participant', $participant);
        } elseif ($filter === 'participate' && $participant) {
            $qb->innerJoin('t.participants', 'p')
               ->andWhere('p = :participant')
               ->setParameter('participant', $participant);
        }

        $qb->orderBy('t.dateAndTime', $selectedDateOrder);
        $trips = $qb->getQuery()->getResult();

        foreach ($trips as $trip) {
            $trip->isOrganizer = $trip->getOrganizer() === $participant;
            $trip->isParticipant = $trip->getParticipants()->contains($participant);
        }

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'campuses' => $campuses,
            'selectedCampusId' => $selectedCampusId,
            'selectedFilter' => $filter,
            'selectedDateOrder' => $selectedDateOrder,
            'showPastTrips' => $showPastTrips,
        ]);
    }



    #[Route('/trip/create', name: 'app_trip_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository, ParticipantRepository $participantRepository, StateRepository $stateRepository): Response
    {

        $places = $placeRepository->findAll();

        $placesArray = array_map(function ($place) {
            return [
                'name' => $place->getName(),
                'latitude' => $place->getLatitude(),
                'longitude' => $place->getLongitude(),
                'address' => $place->getAddress(),
            ];
        }, $places);

        $placesJson = json_encode($placesArray);


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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($trip);
                $entityManager->flush();
                $this->addFlash('success', 'Trip successfully added!');
                return $this->redirectToRoute('app_trip_list');
            } else {
                $this->addFlash('error', 'There was an error with your submission. Please check the form and try again.');
            }
        }

        return $this->render("trip/edit.html.twig", ['form' => $form, 'places' => $places, 'placesJson' => $placesJson]);
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

        $registrationDeadline = $trip->getRegistrationDeadline();
        
        if (new \DateTime() > $registrationDeadline) {
            $this->addFlash('error', 'The registration deadline has passed');
            return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
        }

        if ($trip->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'You are already participating in this trip.');
            return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
        }

        if ($trip->getParticipants()->count() >= $trip->getSeats()) {
            $this->addFlash('error', 'The trip is FULL !!!');
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

        $registrationDeadline = $trip->getRegistrationDeadline();
        if (new \DateTime() > $registrationDeadline) {
            $this->addFlash('error', 'The trip cannot be DELETED, because the registration deadline has passed !!!');
            return $this->redirectToRoute('app_trip_detail', ['id' => $id]);
        }

        $entityManager->remove($trip);
        $entityManager->flush();

        $this->addFlash('success', 'Trip successfully deleted!');

        return $this->redirectToRoute('app_trip_list');
    }

    #[Route('/trip/edit/{id}', name: 'app_trip_edit')]
    public function update(int $id, Request $request, TripRepository $tripRepository, EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {
        $trip = $tripRepository->find($id);
        if (!$trip) {
            throw $this->createNotFoundException('Trip not found');
        }
        $places = $placeRepository->findAll();
        $placesArray = array_map(function ($place) {
        return [
            'name' => $place->getName(),
            'latitude' => $place->getLatitude(),
            'longitude' => $place->getLongitude(),
            'address' => $place->getAddress(),
        ];
        }, $places);
        $placesJson = json_encode($placesArray);
        
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
        'places' => $places,
        'placesJson' => $placesJson

        ]);
    }
}
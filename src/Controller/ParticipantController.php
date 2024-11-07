<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ParticipantController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }
    #[Route('/participant/create', name: 'app_participant_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $participant =new Participant();

        $form = $this->createForm(ParticipantFormType::class, $participant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash('success', 'Participant ajoutée !');
            return $this->redirectToRoute('app_main');


        }else{
            
            $this->addFlash('error','veuillez verifer email et votre pseudo');

        }
        return $this->render("participant/edit.html.twig",[

            'form'=>$form

        ]);
    }

    #[Route('/participant/profil', name: 'app_participant_profil')]
    public function profil(): Response
    {

        $user = $this->getUser();
        dd($user);

        return $this->render('participant/profil.html.twig', [
            'controller_name' => 'ParticipantController',
            'user' => $user
        ]);
    }


}




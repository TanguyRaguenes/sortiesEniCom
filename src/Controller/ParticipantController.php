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
use App\Repository\ParticipantRepository;


class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
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
            $file = $form['photoProfil']->getData();

            if($file){
            $filename =uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('photo_directory'), $filename);
            $participant->setphotoProfil($filename);

            }


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
    public function profil(ParticipantRepository $participantRepository): Response
    {

        $user = $this->getUser();
        
        $participant = $participantRepository->findOneByEmail($user->getEmail());
        // dd($participant);

        return $this->render('participant/profil.html.twig',[
            'controller_name' => 'ParticipantController',
            'participant' => $participant


        ]);
    }
            #[Route('/participant/edit/{id}', name: 'app_participant_edit')]
                public function update(int $id, Request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
                {
                    $participant = $participantRepository->find($id);

                    if (!$participant) {
                        throw $this->createNotFoundException('Participant not found');
                    }
                                        
                    $form = $this->createForm(ParticipantFormType::class, $participant, [
                    'is_edit' => true,
                     ]);
                    $form->handleRequest($request);
                    
                    

                    if ($form->isSubmitted() && $form->isValid()) {
                        $file = $form['photoProfil']->getData();
                        if($file){
                            $filename = uniqid().'.'. $file->guessExtension();
                            $file->move($this->getParameter('photo_directory'), $filename);
                            $participant->setphotoProfil($filename);
                        }
                        
                        
                        $entityManager->flush();
                        $this->addFlash('success', 'Participant updated !');
                        return $this->redirectToRoute('app_participant_profil');
                    }

                    return $this->render("participant/edit.html.twig", [
                        'form' => $form,
                        'participant' => $participant,
                    ]);
    }







    }





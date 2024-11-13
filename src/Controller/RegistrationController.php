<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

<<<<<<< HEAD
    
=======
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
>>>>>>> 4d6c2bdd9f124271f0bb25975fc61d903b9020d7

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $participant = new Participant();
            $participant->setEmail($user->getEmail());
             $uniqueId = substr(Uuid::v4()->toBase58(), 0, 8);
             
            $participant->setName('name');
            $participant->setFirstName('first name');
            $participant->setUsername('User_' . $uniqueId);
            $participant->setPhone(0000000000);
            $participant->setPhotoProfil('default.jpg');

            $entityManager->persist($user);
            $entityManager->flush();
             $entityManager->persist($participant);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
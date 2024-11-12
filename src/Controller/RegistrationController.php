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

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

    

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $participant = new Participant();
            $participant->setEmail($user->getEmail());
            $participant->setName('Default name');
            $participant->setFirstName('Default first name');
            $participant->setUsername('Default username');
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
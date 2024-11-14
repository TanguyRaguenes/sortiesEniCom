<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Campus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_users_list')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $campuses = $entityManager->getRepository(Campus::class)->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'campuses' => $campuses,
        ]);
    }

    #[Route('/user/{id}/role', name: 'admin_user_change_role')]
    public function changeUserRole(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $newRole = $request->request->get('role');
            if (in_array($newRole, ['ROLE_USER', 'ROLE_ORGANIZER', 'ROLE_ADMIN'])) {
                $user->setRoles([$newRole]);
                $entityManager->flush();
                $this->addFlash('success', 'Rôle modifié avec succès !');
            }
        }

        return $this->redirectToRoute('admin_users_list');
    }

    #[Route('/campus/{id}/delete', name: 'admin_campus_delete')]
    public function deleteCampus(Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();

        $this->addFlash('success', 'Campus supprimé avec succès !');

        return $this->redirectToRoute('admin_users_list');
    }

    #[Route('/campus/create', name: 'admin_campus_create', methods: ['GET', 'POST'])]
    public function createCampus(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $location = $request->request->get('location');

            if ($location) {
                $campus = new Campus();
                $campus->setLocation($location);

                $entityManager->persist($campus);
                $entityManager->flush();

                $this->addFlash('success', 'Campus créé avec succès !');

                return $this->redirectToRoute('admin_users_list');
            }
        }

        return $this->render('admin/campus_create.html.twig');
    }
}
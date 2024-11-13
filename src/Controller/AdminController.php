<?php

namespace App\Controller;

use App\Entity\User;
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
        
        return $this->render('admin/users.html.twig', [
            'users' => $users
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
}
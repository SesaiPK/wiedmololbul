<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/panel', name: 'panel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Pobierz wszystkich użytkowników
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/panel.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/delete-user/{id}', name: 'delete_user', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete_user', $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Nieprawidłowy token CSRF!');
            return $this->redirectToRoute('admin_panel');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Użytkownik został usunięty.');
        return $this->redirectToRoute('admin_panel');
    }
}

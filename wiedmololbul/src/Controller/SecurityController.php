<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        dd($this->getUser()); // Powinien zwrócić encję User, jeśli logowanie działa
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('homepage');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmedPassword = $request->request->get('confirmedPassword');

            // Sprawdzenie czy pola są uzupełnione
            if (!$email || !$password || !$confirmedPassword) {
                $this->addFlash('error', 'Wszystkie pola są wymagane.');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy e-mail jest poprawny
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Niepoprawny format e-maila.');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy hasła się zgadzają
            if ($password !== $confirmedPassword) {
                $this->addFlash('error', 'Hasła nie są identyczne.');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy użytkownik już istnieje
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Użytkownik z tym adresem email już istnieje.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $password)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Konto zostało utworzone. Możesz się zalogować.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig');
    }

}

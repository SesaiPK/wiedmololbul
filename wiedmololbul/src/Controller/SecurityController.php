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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        dd($this->getUser()); // Powinien zwrócić encję User, jeśli logowanie działa
    }

    #[Route(path: '/security/login', name: 'app_login', methods: ['POST','GET'])]
    /**
     * Logowanie użytkownika.
     *
     * @OA\Post(
     *     path="/login",
     *     summary="Logowanie użytkownika",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Zalogowano pomyślnie."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nieprawidłowe dane logowania."
     *     )
     * )
     */
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

    #[Route('/security/register', name: 'app_register', methods: ['POST','GET'])]
    /**
     * Rejestracja użytkownika.
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmedPassword = $request->request->get('confirmedPassword');

            // Sprawdzenie czy pola są uzupełnione
            if (!$email || !$password || !$confirmedPassword) {
                $this->addFlash('error', 'All fields are required');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy e-mail jest poprawny
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Wrong email format');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy hasła się zgadzają
            if ($password !== $confirmedPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('app_register');
            }

            // Sprawdzenie, czy użytkownik już istnieje
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'User with this email already exists.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $password)
            );
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account created.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig');
    }

    #[Route('/security/change-password', name: 'app_change_password', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    /**
     * Zmiana hasła użytkownika.
     *
     * @OA\Patch(
     *     path="/security/change-password",
     *     summary="Zmiana hasła użytkownika",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password"},
     *             @OA\Property(property="current_password", type="string", example="oldPassword123"),
     *             @OA\Property(property="new_password", type="string", example="newSecurePassword456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hasło zostało zmienione."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Nieprawidłowe hasło."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nieautoryzowany dostęp."
     *     )
     * )
     */
    public function changePassword(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager
    ): Response
    {
        $user = $this->getUser();

        $currentPassword = $request->request->get('current_password');
        $newPassword = $request->request->get('new_password');

        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Wrong current password.');
            return $this->redirectToRoute('app_profile');
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $entityManager->flush();

        $this->addFlash('success', 'Password changed.');
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/security/delete-account', name: 'app_delete_account', methods: ['POST'])]
    /**
     * Usuwanie użytkownika
     */
    #[IsGranted("ROLE_USER")]
    public function deleteAccount(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $request->getSession()->invalidate(); // Usunięcie sesji
        $this->container->get('security.token_storage')->setToken(null); // Wyczyszczenie tokenu użytkownika

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

}

<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/post')]
class PostController extends AbstractController
{
    /**
     * Lista wszystkich postów
     *
     * @OA\Get(
     *     path="/post/",
     *     summary="Lista postów",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Zwraca listę postów",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    /**
     * Tworzenie nowego posta
     *
     * @OA\Post(
     *     path="/post/new",
     *     summary="Tworzenie nowego posta",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name", "content"},
     *             @OA\Property(property="name", type="string", example="Tytuł posta"),
     *             @OA\Property(property="content", type="string", example="Treść posta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post został stworzony"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Nieprawidłowe dane wejściowe"
     *     )
     * )
     */
    #[Route('/new', name: 'post_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser()->getEmail());
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post został dodany!');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('post/create.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    /**
     * Wyświetlanie szczegółów posta
     *
     * @OA\Get(
     *     path="/post/{id}",
     *     summary="Wyświetlanie szczegółów posta",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID posta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Szczegóły posta"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post nie znaleziony"
     *     )
     * )
     */
    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * Edycja posta
     *
     * @OA\Put(
     *     path="/post/{id}/edit",
     *     summary="Edycja posta",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID posta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name", "content"},
     *             @OA\Property(property="name", type="string", example="Tytuł posta"),
     *             @OA\Property(property="content", type="string", example="Treść posta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post został zaktualizowany"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Brak uprawnień do edycji"
     *     )
     * )
     */
    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getEmail() !== $post->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do edycji tego posta.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Post został zaktualizowany!');
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    /**
     * Usuwanie posta
     *
     * @OA\Delete(
     *     path="/post/{id}/delete",
     *     summary="Usuwanie posta",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID posta",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post został usunięty"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Brak uprawnień do usunięcia"
     *     )
     * )
     */
    #[Route('/{id}/delete', name: 'post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getEmail() !== $post->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do usunięcia tego posta.');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post został usunięty.');
        }

        return $this->redirectToRoute('homepage');
    }
}

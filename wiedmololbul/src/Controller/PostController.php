<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/article')]
class PostController extends AbstractController
{
    /**
     * Pobiera szczegóły artykułu.
     *
     * @OA\Response(
     *     response=200,
     *     description="Szczegóły artykułu"
     * )
     * @OA\Tag(name="Articles")
     */
    #[Route('/{id}', name: 'api_article_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $article = [
            'id' => $id,
            'title' => 'Tytuł artykułu',
            'content' => 'Treść artykułu...'
        ];

        return $this->json($article);
    }

    /**
     * Tworzy nowy artykuł.
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="title", type="string"),
     *         @OA\Property(property="content", type="string")
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Artykuł został utworzony"
     * )
     * @OA\Tag(name="Articles")
     */
    #[Route('', name: 'api_article_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $newArticle = [
            'id' => rand(1, 100),
            'title' => $data['title'],
            'content' => $data['content']
        ];

        return $this->json($newArticle, 201);
    }

    /**
     * Aktualizuje artykuł.
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="title", type="string", nullable=true),
     *         @OA\Property(property="content", type="string", nullable=true)
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Artykuł został zaktualizowany"
     * )
     * @OA\Tag(name="Articles")
     */
    #[Route('/{id}', name: 'api_article_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $updatedArticle = [
            'id' => $id,
            'title' => $data['title'] ?? 'Zaktualizowany tytuł',
            'content' => $data['content'] ?? 'Zaktualizowana treść'
        ];

        return $this->json($updatedArticle);
    }

    /**
     * Usuwa artykuł.
     *
     * @OA\Response(
     *     response=200,
     *     description="Artykuł został usunięty"
     * )
     * @OA\Tag(name="Articles")
     */
    #[Route('/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->json(['message' => "Artykuł o ID $id został usunięty."]);
    }


    #[Route('/articles', name: 'api_articles_list', methods: ['GET'])]
    public function articlesList(): JsonResponse
    {
        // Przykładowe dane artykułów, w przyszłości pobrane z bazy danych.
        $articles = [
            ['id' => 1, 'title' => 'Pierwszy artykuł', 'content' => 'Treść pierwszego artykułu'],
            ['id' => 2, 'title' => 'Drugi artykuł', 'content' => 'Treść drugiego artykułu'],
            ['id' => 3, 'title' => 'Trzeci artykuł', 'content' => 'Treść trzeciego artykułu'],
        ];

        return $this->json($articles);
    }


}

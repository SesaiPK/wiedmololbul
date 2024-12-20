<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class PostController extends AbstractController
{
    #[Route('/article/{id}', name: 'article_show')]
    public function show(int $id): Response
    {
        // Znajdź artykuł z bazy (to później, gdy będziesz mieć encje i repozytoria)
        $article = [
            'id' => $id,
            'title' => 'Tytuł artykułu',
            'content' => 'Treść artykułu...'
        ];

        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }
}
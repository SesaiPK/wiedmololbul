<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/{slug}', name: 'article_show')]
    public function show(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
}

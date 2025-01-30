<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'homepage')]
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $query = $request->query->get('search'); // Pobranie wartości z searchbara

        if ($query) {
            $posts = $postRepository->searchPosts($query); // Wyszukaj posty
        } else {
            $posts = $postRepository->findAllPosts();
        }


        return $this->render('homepage.html.twig', [
            'posts' => $posts
        ]);
    }
}

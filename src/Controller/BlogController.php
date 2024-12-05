<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(PostRepository $postRepository): Response
    {
        // Fetch posts for the blog view (you can filter if needed, for example, fetching only published posts)
        $posts = $postRepository->findBy([], ['id' => 'DESC']); // Sorting by created date, newest first

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,  // Pass the posts to the template
        ]);
    }
}

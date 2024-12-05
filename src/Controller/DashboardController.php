<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $userRepository, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $totalComment = $user->getComments()->count();
        $totalUser = $userRepository->count([]);
        $totalPost = $user->getPosts()->count();
        return $this->render('dashboard/index.html.twig', [
            'totalPost' => $totalPost,
            'totalUser' => $totalUser,
            'totalComment' => $totalComment,
        ]);
    }
}

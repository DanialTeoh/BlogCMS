<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $id = $this->getUser();

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy( ['author' => $id->getId()], ['id' => 'DESC']),
        ]); /* untuk differentiate setiap user |and| kalau create barang baru it will go to the top instead of bottom*/
    }

    #[Route('{id}/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, User $user, FileUploader $fileUploader): Response
    {
        $post = new Post();
        $post->setAuthor($user);

        $post->setCreatedAt(new \DateTimeImmutable ()); // ni untuk set date terkini
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postPicture */

            $postPicture = $form->get('postPicture')->getData(); //nama kat sini yang kena tukar

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($postPicture) {
                $picture = $fileUploader->upload($postPicture);
                $post->setPostPicture($picture);
            }
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Create a new Comment object and link it to the post
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($this->getUser());  // Assuming the user is authenticated

        // Create the form for the comment
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        // If the comment form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the comment to the database
            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirect to the same post page to see the comment
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure the logged-in user is the author
            if ($this->getUser() !== $post->getAuthor()) {
                throw $this->createAccessDeniedException('You cannot edit this post.');
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}

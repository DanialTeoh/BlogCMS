<?php

namespace App\Controller;

use App\Entity\Flag;
use App\Entity\Rujukan;
use App\Form\RujukanType;
use App\Repository\RujukanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rujukan')]
final class RujukanController extends AbstractController
{
    #[Route(name: 'app_rujukan_index', methods: ['GET'])]
    public function index(RujukanRepository $rujukanRepository): Response
    {
        return $this->render('rujukan/index.html.twig', [
            'rujukans' => $rujukanRepository->findAll(),
            'flag' => '',
        ]);
    }

    #[Route('/{flag}/new', name: 'app_rujukan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Flag $flag): Response
    {
        $rujukan = new Rujukan();
        $rujukan->setFlag($flag);
        $form = $this->createForm(RujukanType::class, $rujukan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rujukan);
            $entityManager->flush();

            return $this->redirectToRoute('app_rujukan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rujukan/new.html.twig', [
            'rujukan' => $rujukan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rujukan_show', methods: ['GET'])]
    public function show(Rujukan $rujukan): Response
    {
        return $this->render('rujukan/show.html.twig', [
            'rujukan' => $rujukan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rujukan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rujukan $rujukan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RujukanType::class, $rujukan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rujukan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rujukan/edit.html.twig', [
            'rujukan' => $rujukan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/list', name: 'app_rujukan_list', methods: ['GET'])]
    public function list(Request $request,RujukanRepository $rujukanRepository): Response
    {
        $flag = $request->get('id');
        return $this->render('rujukan/index.html.twig', [
            'rujukans' => $rujukanRepository->findBy(['flag'=>$flag]),
            'flag' => $flag
        ]);

    }

    #[Route('/{id}', name: 'app_rujukan_delete', methods: ['POST'])]
    public function delete(Request $request, Rujukan $rujukan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rujukan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rujukan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rujukan_index', [], Response::HTTP_SEE_OTHER);
    }
}

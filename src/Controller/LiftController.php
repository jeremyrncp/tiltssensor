<?php

namespace App\Controller;

use App\Entity\Lift;
use App\Form\LiftType;
use App\Repository\LiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lift')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class LiftController extends AbstractController
{
    #[Route(name: 'app_lift_index', methods: ['GET'])]
    public function index(LiftRepository $liftRepository): Response
    {
        return $this->render('lift/index.html.twig', [
            'lifts' => $liftRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lift_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lift = new Lift();
        $form = $this->createForm(LiftType::class, $lift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lift);
            $entityManager->flush();

            return $this->redirectToRoute('app_lift_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lift/new.html.twig', [
            'lift' => $lift,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lift_show', methods: ['GET'])]
    public function show(Lift $lift): Response
    {
        return $this->render('lift/show.html.twig', [
            'lift' => $lift,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lift_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lift $lift, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LiftType::class, $lift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lift_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lift/edit.html.twig', [
            'lift' => $lift,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lift_delete', methods: ['POST'])]
    public function delete(Request $request, Lift $lift, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lift->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lift);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lift_index', [], Response::HTTP_SEE_OTHER);
    }
}

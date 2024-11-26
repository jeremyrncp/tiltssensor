<?php

namespace App\Controller;

use App\Entity\ApiCredentials;
use App\Form\ApiCredentialsType;
use App\Repository\ApiCredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/credentials')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class ApiCredentialsController extends AbstractController
{
    #[Route(name: 'app_api_credentials_index', methods: ['GET'])]
    public function index(ApiCredentialsRepository $apiCredentialsRepository): Response
    {
        return $this->render('api_credentials/index.html.twig', [
            'api_credentials' => $apiCredentialsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_api_credentials_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $apiCredential = new ApiCredentials();
        $form = $this->createForm(ApiCredentialsType::class, $apiCredential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apiCredential);
            $entityManager->flush();

            return $this->redirectToRoute('app_api_credentials_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api_credentials/new.html.twig', [
            'api_credential' => $apiCredential,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_credentials_show', methods: ['GET'])]
    public function show(ApiCredentials $apiCredential): Response
    {
        return $this->render('api_credentials/show.html.twig', [
            'api_credential' => $apiCredential,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_api_credentials_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiCredentials $apiCredential, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApiCredentialsType::class, $apiCredential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_api_credentials_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api_credentials/edit.html.twig', [
            'api_credential' => $apiCredential,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_credentials_delete', methods: ['POST'])]
    public function delete(Request $request, ApiCredentials $apiCredential, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiCredential->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($apiCredential);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_api_credentials_index', [], Response::HTTP_SEE_OTHER);
    }
}

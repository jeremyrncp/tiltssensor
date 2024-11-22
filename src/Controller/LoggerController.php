<?php

namespace App\Controller;

use App\Entity\Logger;
use App\Form\LoggerType;
use App\Repository\LoggerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/logger')]
final class LoggerController extends AbstractController
{
    #[Route(name: 'app_logger_index', methods: ['GET'])]
    public function index(LoggerRepository $loggerRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $loggerRepository->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('logger/index.html.twig', [
            'loggers' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_logger_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $logger = new Logger();
        $form = $this->createForm(LoggerType::class, $logger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($logger);
            $entityManager->flush();

            return $this->redirectToRoute('app_logger_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logger/new.html.twig', [
            'logger' => $logger,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logger_show', methods: ['GET'])]
    public function show(Logger $logger): Response
    {
        return $this->render('logger/show.html.twig', [
            'logger' => $logger,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_logger_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Logger $logger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoggerType::class, $logger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_logger_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logger/edit.html.twig', [
            'logger' => $logger,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logger_delete', methods: ['POST'])]
    public function delete(Request $request, Logger $logger, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logger->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logger);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logger_index', [], Response::HTTP_SEE_OTHER);
    }
}

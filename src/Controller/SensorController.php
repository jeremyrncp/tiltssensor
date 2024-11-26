<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Entity\User;
use App\Form\SensorType;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sensor')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class SensorController extends AbstractController
{
    #[Route(name: 'app_sensor_index', methods: ['GET'])]
    public function index(SensorRepository $sensorRepository, Request $request, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted("ROLE_SUPER_ADMIN")) {
            $sensors = $sensorRepository->findAll();
        } elseif ($this->isGranted("ROLE_ADMIN")) {
            $sensors = $sensorRepository->findByOrganization($user->getOrganization());
        }

        $pagination = $paginator->paginate(
            $sensors,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('sensor/index.html.twig', [
            'sensors' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_sensor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sensor = new Sensor();
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sensor);
            $entityManager->flush();

            return $this->redirectToRoute('app_sensor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sensor/new.html.twig', [
            'sensor' => $sensor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sensor_show', methods: ['GET'])]
    public function show(Sensor $sensor): Response
    {
        return $this->render('sensor/show.html.twig', [
            'sensor' => $sensor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sensor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sensor $sensor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sensor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sensor/edit.html.twig', [
            'sensor' => $sensor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sensor_delete', methods: ['POST'])]
    public function delete(Request $request, Sensor $sensor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sensor->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sensor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sensor_index', [], Response::HTTP_SEE_OTHER);
    }
}

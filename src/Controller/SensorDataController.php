<?php

namespace App\Controller;

use App\Entity\SensorData;
use App\Form\SensorDataType;
use App\Repository\SensorDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sensordata')]
#[IsGranted('ROLE_ADMIN')]
final class SensorDataController extends AbstractController
{
    #[Route(name: 'app_sensor_data_index', methods: ['GET'])]
    public function index(SensorDataRepository $sensorDataRepository, Request $request,  PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $sensorDataRepository->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('sensor_data/index.html.twig', [
            'sensor_datas' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_sensor_data_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sensorDatum = new SensorData();
        $form = $this->createForm(SensorDataType::class, $sensorDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sensorDatum);
            $entityManager->flush();

            return $this->redirectToRoute('app_sensor_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sensor_data/new.html.twig', [
            'sensor_datum' => $sensorDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sensor_data_show', methods: ['GET'])]
    public function show(SensorData $sensorDatum): Response
    {
        return $this->render('sensor_data/show.html.twig', [
            'sensor_datum' => $sensorDatum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sensor_data_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SensorData $sensorDatum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SensorDataType::class, $sensorDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sensor_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sensor_data/edit.html.twig', [
            'sensor_datum' => $sensorDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sensor_data_delete', methods: ['POST'])]
    public function delete(Request $request, SensorData $sensorDatum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sensorDatum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sensorDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sensor_data_index', [], Response::HTTP_SEE_OTHER);
    }
}

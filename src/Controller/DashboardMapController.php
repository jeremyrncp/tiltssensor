<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\LiftRepository;
use App\Service\LiftService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;

class DashboardMapController extends AbstractController
{
    public function __construct(
        private readonly LiftRepository $liftRepository
    ) {
    }

    #[Route('/dashboard/map', name: 'app_dashboard_map')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->getUser() instanceof InMemoryUser) {
            $lifts = $this->liftRepository->findAll();
        } else {
            $lifts = $this->liftRepository->findBy([
                'organization' => $user->getOrganization()
            ]);
        }

        return $this->render('dashboard_map/index.html.twig', [
            'lifts' => $lifts,
            'firstLift' => reset($lifts)
        ]);
    }
}

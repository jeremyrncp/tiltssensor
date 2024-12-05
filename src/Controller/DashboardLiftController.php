<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RangeDateLiftType;
use App\Repository\LiftRepository;
use App\Service\LiftService;
use App\VO\RangeDateVO;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;

class DashboardLiftController extends AbstractController
{
    public function __construct(
        private readonly LiftRepository $liftRepository,
        private readonly LiftService $liftService
    ) {
    }

    #[Route('/dashboard/lift', name: 'app_dashboard_lift')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted("ROLE_ADMIN") AND !$this->isGranted("ROLE_SUPERVISOR")) {
            throw new AccessDeniedException("Access denied");
        }

        $rangeDateVO = new RangeDateVO();
        $rangeDateVO->start = (new \DateTime())->modify("-15 days");
        $rangeDateVO->end = (new \DateTime("now"));

        $form = $this->createForm(RangeDateLiftType::class, $rangeDateVO);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($this->getUser() instanceof InMemoryUser) {
            $lifts = $this->liftRepository->findAll();
        } else {
            $lifts = $this->liftRepository->findBy([
                'organization' => $user->getOrganization()
            ]);
        }

        $liftDatas = $this->liftService->getLiftData($lifts, $rangeDateVO->start, $rangeDateVO->end);

        return $this->render('dashboard_lift/index.html.twig', [
            'lifts' => $liftDatas,
            'form' => $form->createView()
        ]);
    }
}

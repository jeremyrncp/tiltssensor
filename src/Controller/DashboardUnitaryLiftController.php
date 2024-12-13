<?php

namespace App\Controller;

use App\Entity\Lift;
use App\Entity\Note;
use App\Entity\User;
use App\Form\NoteType;
use App\Form\RangeDateLiftType;
use App\Service\LiftService;
use App\VO\RangeDateVO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;

class DashboardUnitaryLiftController extends AbstractController
{
    #[Route('/dashboard/lift/{id}', name: 'app_dashboard_unitary_lift')]
    public function index(Lift $lift, Request $request, EntityManagerInterface $entityManager, LiftService $liftService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user instanceof InMemoryUser) {
        }else if ($lift->getOrganization() !== $user->getOrganization() AND !$this->isGranted("ROLE_SUPER_ADMIN")) {
            throw new AccessDeniedHttpException();
        }

        $note = new Note();

        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            $note->setLift($lift)
                ->setOwner($user)
                ->setCreatedAt(new \DateTime());
            $lift->addNote($note);
            $entityManager->flush();
        }

        $rangeDateVO = new RangeDateVO();
        $rangeDateVO->start = (new \DateTime())->modify("-7 days");
        $rangeDateVO->end = (new \DateTime("now"));

        $formDate = $this->createForm(RangeDateLiftType::class, $rangeDateVO);
        $formDate->handleRequest($request);

        $liftData = $liftService->getLiftData([$lift], $rangeDateVO->start, $rangeDateVO->end);

        return $this->render('dashboard_unitary_lift/index.html.twig', [
            'lift' => $lift,
            'noteForm' => $noteForm->createView(),
            'rangeDateForm' => $formDate->createView(),
            'liftData' => reset($liftData)
        ]);
    }
}

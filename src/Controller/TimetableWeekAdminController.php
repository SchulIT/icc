<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\TimetableWeek;
use App\Entity\Week;
use App\Form\TimetableWeekType;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Repository\WeekRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/timetable/weeks')]
#[IsGranted('ROLE_ADMIN')]
class TimetableWeekAdminController extends AbstractController {

    public function __construct(private TimetableWeekRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '/add', name: 'admin_add_timetable_week')]
    public function add(Request $request): Response {
        $week = new TimetableWeek();
        $form = $this->createForm(TimetableWeekType::class, $week);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($week);

            $this->addFlash('success', 'admin.timetable.weeks.add.success');
            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/weeks/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'admin_edit_timetable_week')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] TimetableWeek $week, Request $request): Response {
        $form = $this->createForm(TimetableWeekType::class, $week);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($week);

            $this->addFlash('success', 'admin.timetable.weeks.add.success');
            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/weeks/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/add/default', name: 'add_default_timetable_weeks')]
    public function addDefaultWeeks(TranslatorInterface $translator, WeekRepositoryInterface $weekRepository): Response {
        $weeks = $this->repository->findAll();

        if(count($weeks) > 0) {
            $this->addFlash('error', 'admin.timetable.weeks.add_default.error');
            return $this->redirectToRoute('admin_timetable');
        }

        $allWeeks = $weekRepository->findAll();

        for($weekMod = 0; $weekMod <= 1; $weekMod++) {
            $weeks = array_filter($allWeeks, fn(Week $week) => $week->getNumber() % 2 == $weekMod);

            $week = (new TimetableWeek())
                ->setDisplayName($translator->trans(sprintf('admin.timetable.weeks.add_default.weeks.%d.label', $weekMod)))
                ->setKey($translator->trans(sprintf('admin.timetable.weeks.add_default.weeks.%d.key', $weekMod)));

            foreach($weeks as $w) {
                $week->addWeek($w);
            }

            $this->repository->persist($week);
        }

        $this->addFlash('success', 'admin.timetable.weeks.add_default.success');
        return $this->redirectToRoute('admin_timetable');
    }

    #[Route(path: '/{uuid}/remove', name: 'admin_remove_timetable_week')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] TimetableWeek $week, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.timetable.weeks.remove.confirm',
            'message_parameters' => [
                '%name%' => $week->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($week);

            $this->addFlash('success', 'admin.timetable.weeks.remove.success');

            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/weeks/remove.html.twig', [
            'form' => $form->createView(),
            'week' => $week
        ]);
    }
}
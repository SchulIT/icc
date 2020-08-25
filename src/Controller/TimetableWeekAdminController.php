<?php

namespace App\Controller;

use App\Entity\TimetableWeek;
use App\Form\TimetableWeekType;
use App\Repository\TimetableWeekRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/timetable/weeks")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TimetableWeekAdminController extends AbstractController {

    private $repository;

    public function __construct(TimetableWeekRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("/add", name="admin_add_timetable_week")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{uuid}/edit", name="admin_edit_timetable_week")
     */
    public function edit(TimetableWeek $week, Request $request) {
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

    /**
     * @Route("/add/default", name="add_default_timetable_weeks")
     */
    public function addDefaultWeeks(TranslatorInterface $translator) {
        $weeks = $this->repository->findAll();

        if(count($weeks) > 0) {
            $this->addFlash('error', 'admin.timetable.weeks.add_default.error');
            return $this->redirectToRoute('admin_timetable');
        }

        for($weekMod = 0; $weekMod <= 1; $weekMod++) {
            $week = (new TimetableWeek())
                ->setWeekMod($weekMod)
                ->setDisplayName($translator->trans(sprintf('admin.timetable.weeks.add_default.weeks.%d.label', $weekMod)))
                ->setKey($translator->trans(sprintf('admin.timetable.weeks.add_default.weeks.%d.key', $weekMod)));

            $this->repository->persist($week);
        }

        $this->addFlash('success', 'admin.timetable.weeks.add_default.success');
        return $this->redirectToRoute('admin_timetable');
    }

    /**
     * @Route("/{uuid}/remove", name="admin_remove_timetable_week")
     */
    public function remove(TimetableWeek $week, Request $request) {
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
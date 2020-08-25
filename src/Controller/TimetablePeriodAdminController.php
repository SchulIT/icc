<?php

namespace App\Controller;

use App\Entity\TimetablePeriod;
use App\Form\TimetablePeriodType;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TimetablePeriodStrategy;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/timetable/periods")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TimetablePeriodAdminController extends AbstractController {

    private $repository;

    public function __construct(TimetablePeriodRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("/add", name="admin_add_timetable_period")
     */
    public function add(Request $request) {
        $period = new TimetablePeriod();

        $form = $this->createForm(TimetablePeriodType::class, $period);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($period);

            $this->addFlash('success', 'admin.timetable.periods.add.success');
            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/periods/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="admin_edit_timetable_period")
     */
    public function edit(TimetablePeriod $period, Request $request) {
        $form = $this->createForm(TimetablePeriodType::class, $period);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($period);

            $this->addFlash('success', 'admin.timetable.periods.edit.success');
            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/periods/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="admin_remove_timetable_period")
     */
    public function remove(TimetablePeriod $period, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.timetable.periods.remove.confirm',
            'message_parameters' => [
                '%name%' => $period->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($period);

            $this->addFlash('success', 'admin.timetable.periods.remove.success');

            return $this->redirectToRoute('admin_timetable');
        }

        return $this->render('admin/timetable/periods/remove.html.twig', [
            'form' => $form->createView(),
            'period' => $period
        ]);
    }
}
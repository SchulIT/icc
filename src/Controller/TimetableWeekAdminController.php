<?php

namespace App\Controller;

use App\Entity\TimetableWeek;
use App\Form\TimetableWeekType;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("", name="admin_timetable_weeks")
     */
    public function index() {
        return $this->render('admin/timetable/weeks/index.html.twig', [
            'weeks' => $this->repository->findAll()
        ]);
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
            return $this->redirectToRoute('admin_timetable_weeks');
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
            return $this->redirectToRoute('admin_timetable_weeks');
        }

        return $this->render('admin/timetable/weeks/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="admin_remove_timetable_week")
     */
    public function remove(TimetableWeek $week, Request $request) {

    }
}
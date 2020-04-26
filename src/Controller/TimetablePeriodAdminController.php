<?php

namespace App\Controller;

use App\Entity\TimetablePeriod;
use App\Form\TimetablePeriodType;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TimetablePeriodStrategy;
use SchoolIT\CommonBundle\Utils\RefererHelper;
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
     * @Route("", name="admin_timetable_periods")
     */
    public function index(Sorter $sorter) {
        $periods = $this->repository->findAll();
        $sorter->sort($periods, TimetablePeriodStrategy::class);

        return $this->render('admin/timetable/periods/index.html.twig', [
            'periods' => $periods
        ]);
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

            $this->addFlash('success', '...');
            return $this->redirectToRoute('admin_timetable_periods');
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

            $this->addFlash('success', '...');
            return $this->redirectToRoute('admin_timetable_periods');
        }

        return $this->render('admin/timetable/periods/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="admin_remove_timetable_period")
     */
    public function remove(TimetablePeriod $period, Request $request) {

    }
}
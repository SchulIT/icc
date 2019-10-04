<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Grouping\AppointmentDateStrategy as AppointmentGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\AppointmentRepositoryInterface;
use App\Sorting\AppointmentDateGroupStrategy;
use App\Sorting\Sorter;
use App\Utils\RefererHelper;
use App\View\Filter\AppointmentCategoryFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Sorting\AppointmentDateStrategy as AppointmentSortingStrategy;

/**
 * @Route("/admin/appointments")
 * @Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")
 */
class AppointmentAdminController extends AbstractController {

    private $repository;
    private $grouper;
    private $sorter;

    public function __construct(AppointmentRepositoryInterface $appointmentRepository, Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $appointmentRepository;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="admin_appointments")
     */
    public function index(AppointmentCategoryFilter $categoryFilter, ?int $categoryId = null, ?string $q = null) {
        $categoryFilterView = $categoryFilter->handle($categoryId);
        $appointments = $this->repository->findAll($categoryFilterView->getCurrentCategory(), $q);

        $groups = $this->grouper->group($appointments, AppointmentGroupingStrategy::class);
        $this->sorter->sortGroupItems($groups, AppointmentSortingStrategy::class);
        $this->sorter->sort($groups, AppointmentDateGroupStrategy::class);

        return $this->render('admin/appointments/index.html.twig', [
            'groups' => $groups,
            'categoryFilter' => $categoryFilterView,
            'q' => $q
        ]);
    }

    /**
     * @Route("/add", name="add_appointment")
     */
    public function add(Request $request) {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($appointment);

            $this->addFlash('success', 'admin.appointments.new.success');
            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/appointments/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_appointment")
     */
    public function edit(Appointment $appointment, Request $request) {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($appointment);

            $this->addFlash('success', 'admin.appointments.new.success');
            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/appointments/edit.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_appointment")
     */
    public function remove() {

    }
}
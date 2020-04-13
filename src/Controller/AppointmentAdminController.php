<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Form\AppointmentType;
use App\Grouping\AppointmentDateStrategy as AppointmentGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\AppointmentRepositoryInterface;
use App\Sorting\AppointmentDateGroupStrategy;
use App\Sorting\Sorter;
use App\Utils\RefererHelper;
use App\View\Filter\AppointmentCategoriesFilter;
use App\View\Filter\AppointmentCategoryFilter;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Sorting\AppointmentDateStrategy as AppointmentSortingStrategy;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function index(AppointmentCategoryFilter $categoryFilter, Request $request) {
        $q = $request->query->get('q', null);
        $categoryFilterView = $categoryFilter->handle($request->query->get('category', null));
        $categories = $categoryFilterView->getCurrentCategory() === null ? [ ] : [$categoryFilterView->getCurrentCategory()];
        $appointments = $this->repository->findAll($categories, $q);

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
     * @Route("/{uuid}/edit", name="edit_appointment")
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
     * @Route("/{uuid}/remove", name="remove_appointment")
     */
    public function remove(Appointment $appointment, Request $request, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.appointments.remove.confirm', [
                '%name%' => $appointment->getTitle()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($appointment);

            $this->addFlash('success', 'admin.appointments.remove.success');

            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/appointments/remove.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }
}
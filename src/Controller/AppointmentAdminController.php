<?php

namespace App\Controller;

use App\Converter\UserStringConverter;
use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\User;
use App\Event\AppointmentConfirmedEvent;
use App\Form\AppointmentType;
use App\Grouping\AppointmentDateStrategy as AppointmentGroupingStrategy;
use App\Grouping\Grouper;
use App\Repository\AppointmentRepositoryInterface;
use App\Security\Voter\AppointmentVoter;
use App\Sorting\AppointmentDateGroupStrategy;
use App\Sorting\Sorter;
use App\View\Filter\AppointmentCategoriesFilter;
use App\View\Filter\AppointmentCategoryFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Sorting\AppointmentDateStrategy as AppointmentSortingStrategy;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/appointments")
 * @Security("is_granted('ROLE_APPOINTMENT_CREATOR')")
 */
class AppointmentAdminController extends AbstractController {

    private const NumberOfAppointments = 25;

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
        $page = $request->query->getInt('page');
        /** @var User|null $createdBy */
        $createdBy = $this->isGranted('ROLE_APPOINTMENTS_ADMIN') ? null : $this->getUser();

        $paginator = $this->repository->getPaginator(static::NumberOfAppointments, $page, $categories, $q, $createdBy);
        $pages = 1;

        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / static::NumberOfAppointments);
        }

        $appointments = [ ];

        foreach($paginator->getIterator() as $appointment) {
            $appointments[] = $appointment;
        }

        $groups = $this->grouper->group($appointments, AppointmentGroupingStrategy::class);
        $this->sorter->sortGroupItems($groups, AppointmentSortingStrategy::class);
        $this->sorter->sort($groups, AppointmentDateGroupStrategy::class);

        return $this->render('admin/appointments/index.html.twig', [
            'groups' => $groups,
            'pages' => $pages,
            'page' => $page,
            'categoryFilter' => $categoryFilterView,
            'q' => $q
        ]);
    }

    /**
     * @Route("/add", name="add_appointment")
     */
    public function add(Request $request) {
        $appointment = (new Appointment())
            ->setIsConfirmed(false);

        if($this->isGranted('ROLE_APPOINTMENTS_ADMIN')) {
            $appointment->setIsConfirmed(true);
        }

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
        $this->denyAccessUnlessGranted(AppointmentVoter::Edit, $appointment);

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
        $this->denyAccessUnlessGranted(AppointmentVoter::Remove, $appointment);

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

    /**
     * @Route("/{uuid}/confirm", name="confirm_appointment")
     */
    public function confirm(Appointment $appointment, Request $request, UserStringConverter $converter, EventDispatcherInterface $eventDispatcher) {
        $this->denyAccessUnlessGranted('ROLE_APPOINTMENTS_ADMIN');

        if($appointment->getCreatedBy() === null) {
            return $this->redirectToRoute('admin_appointments');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.appointments.confirm.confirm',
            'message_parameters' => [
                '%name%' => $appointment->getTitle(),
                '%user%' => $converter->convert($appointment->getCreatedBy())
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointment->setIsConfirmed(true);
            $this->repository->persist($appointment);

            /** @var User $user */
            $user = $this->getUser();

            $eventDispatcher->dispatch(new AppointmentConfirmedEvent($appointment, $user));

            $this->addFlash('success', 'admin.appointments.confirm.success');

            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/appointments/confirm.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView()
        ]);
    }
}
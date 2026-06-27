<?php

namespace App\Appointment\Controller;

use App\Appointment\Entity\Appointment;
use App\Appointment\Event\AppointmentConfirmedEvent;
use App\Appointment\Form\AppointmentType;
use App\Appointment\Import\OpenHolidaysApi\Importer;
use App\Appointment\Import\OpenHolidaysApi\ImportRequest;
use App\Appointment\Import\OpenHolidaysApi\ImportRequestType;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Appointment\View\Filter\AppointmentCategoryFilter;
use App\Appointment\Voter\AppointmentVoter;
use App\Common\Converter\UserStringConverter;
use App\Common\Entity\User;
use App\Framework\Controller\AbstractController;
use App\Framework\Repository\PaginationQuery;
use Exception;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/appointments')]
#[IsGranted('ROLE_APPOINTMENT_CREATOR')]
class AppointmentAdminController extends AbstractController {

    private const NumberOfAppointments = 25;

    public function __construct(private AppointmentRepositoryInterface $repository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_appointments')]
    public function index(AppointmentCategoryFilter $categoryFilter, Request $request): Response {
        $q = $request->query->get('q', null);
        $categoryFilterView = $categoryFilter->handle($request->query->get('category', null));
        $categories = $categoryFilterView->getCurrentCategory() === null ? [ ] : [$categoryFilterView->getCurrentCategory()];
        $onlyConfirmed = $request->query->get('confirmed') === '✓' ? true : ($request->query->get('confirmed') === '✗' ? false : null);

        $page = $request->query->getInt('page');
        /** @var User|null $createdBy */
        $createdBy = $this->isGranted('ROLE_APPOINTMENTS_ADMIN') ? null : $this->getUser();

        $appointments = $this->repository->findPaginated(new PaginationQuery(page: $page, limit: self::NumberOfAppointments), $categories, $q, $createdBy, $onlyConfirmed ?? null);

        return $this->render('admin/appointments/index.html.twig', [
            'appointments' => $appointments,
            'categoryFilter' => $categoryFilterView,
            'q' => $q,
            'confirmed' => $onlyConfirmed,
            'notConfirmedCount' => $this->repository->countNotConfirmed()
        ]);
    }

    #[Route(path: '/add', name: 'add_appointment')]
    public function add(Request $request): Response {
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

    #[Route(path: '/{uuid}/edit', name: 'edit_appointment')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Appointment $appointment, Request $request): Response {
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

    #[Route(path: '/{uuid}/remove', name: 'remove_appointment')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Appointment $appointment, Request $request, TranslatorInterface $translator): Response {
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

    #[Route(path: '/{uuid}/confirm', name: 'confirm_appointment')]
    public function confirm(#[MapEntity(mapping: ['uuid' => 'uuid'])] Appointment $appointment, Request $request, UserStringConverter $converter, EventDispatcherInterface $eventDispatcher): Response {
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

    #[Route('/import', name: 'import_appointments')]
    public function import(
        Request $request,
        Importer $importer,
        TranslatorInterface $translator
    ): Response {
        $importRequest = new ImportRequest();
        $form = $this->createForm(ImportRequestType::class, $importRequest);
        $form->handleRequest($request);

        $importException = null;

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $result = $importer->import($importRequest);

                $this->addFlash('success',
                    $translator->trans('admin.appointments.import.success',
                        [
                            '%added%' => $result->added,
                            '%updated%' => $result->updated,
                        ]
                    )
                );

                return $this->redirectToRoute('admin_appointments');
            } catch (Exception $e) {
                $importException = $e;
            }
        }

        return $this->render('admin/appointments/import.html.twig', [
            'form' => $form->createView(),
            'exception' => $importException,
        ]);
    }
}

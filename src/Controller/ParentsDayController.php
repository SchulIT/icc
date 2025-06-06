<?php

namespace App\Controller;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\AppointmentsCreatorParamsType;
use App\Form\BookParentsDayAppointmentType;
use App\Form\CancelParentsDayAppointmentType;
use App\Form\ParentsDayAppointmentType;
use App\Form\ParentsDayParentalInformationType;
use App\ParentsDay\AppointmentsCreator;
use App\ParentsDay\AppointmentsCreatorParams;
use App\ParentsDay\ParentsDayParentalInformationResolver;
use App\ParentsDay\TeacherOverviewHelper;
use App\Repository\ParentsDayAppointmentRepositoryInterface;
use App\Repository\ParentsDayParentalInformationRepositoryInterface;
use App\Repository\ParentsDayRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ParentsDayAppointmentVoter;
use App\Sorting\ParentsDayAppointmentStrategy;
use App\Sorting\Sorter;
use App\View\Filter\ParentsDayFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudentFilterView;
use App\View\Filter\TeacherFilter;
use App\View\Filter\TuitionFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parents_day')]
#[IsFeatureEnabled(Feature::ParentsDay)]
#[IsGranted(ParentsDayAppointmentVoter::VIEW)]
class ParentsDayController extends AbstractController {

    public function __construct(RefererHelper $redirectHelper, private readonly ParentsDayRepositoryInterface $parentsDayRepository, private readonly ParentsDayAppointmentRepositoryInterface $appointmentRepository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'parents_day')]
    public function index(DateHelper $dateHelper, TeacherFilter $teacherFilter,
                          StudentFilter $studentFilter, ParentsDayFilter $parentsDayFilter,
                          TeacherOverviewHelper $teacherOverviewHelper,
                          SectionResolverInterface $sectionResolver, Request $request, ParentsDayParentalInformationRepositoryInterface $parentalInformationRepository,
                          Sorter $sorter): Response {
        /** @var User $user */
        $user = $this->getUser();

        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionResolver->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionResolver->getCurrentSection(), $user, $studentFilterView->getCurrentStudent() === null);
        $parentsDayFilterView = $parentsDayFilter->handle($request->query->get('day'), $user);

        if($user->isStudentOrParent()) {
            $studentFilterView = new StudentFilterView([], null, 0);
        }

        $appointments = [ ];
        $teachers = [ ];

        if($parentsDayFilterView->getCurrentParentsDay() !== null) {
            if($user->isTeacher() && $teacherFilterView->getCurrentTeacher() !== null) {
                $appointments = $this->appointmentRepository->findForTeacher($teacherFilterView->getCurrentTeacher(), $parentsDayFilterView->getCurrentParentsDay());
            } else if($user->isStudentOrParent() || $studentFilterView->getCurrentStudent() !== null) {
                /** @var Student[] $students */
                $students = $user->getStudents()->toArray();

                if($studentFilterView->getCurrentStudent() !== null) {
                    $students = [ $studentFilterView->getCurrentStudent() ];
                }

                $appointments = $this->appointmentRepository->findForStudents($students, $parentsDayFilterView->getCurrentParentsDay());

                foreach($students as $student) {
                    $teachers[] = $teacherOverviewHelper->collectTeachersForStudent($student, $parentsDayFilterView->getCurrentParentsDay());
                }
            }
        }

        $sorter->sort($appointments, ParentsDayAppointmentStrategy::class);

        return $this->render('parents_days/index.html.twig', [
            'parents_days' => $this->parentsDayRepository->findUpcoming($dateHelper->getToday()),
            'appointments' => $appointments,
            'teacherFilter' => $teacherFilterView,
            'studentFilter' => $studentFilterView,
            'parentsDayFilter' => $parentsDayFilterView,
            'teacherOverviews' => $teachers
        ]);
    }

    #[Route('/{uuid}/book', name: 'book_parents_day_appointment_overview')]
    public function book(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, TeacherFilter $teacherFilter, Request $request, SectionResolverInterface $sectionResolver, Sorter $sorter): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::BOOK_ANY, $parentsDay);

        /** @var User $user */
        $user = $this->getUser();

        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionResolver->getCurrentSection(), $user, false, true);


        $appointments = [ ];
        $unavailableAppointments = [ ];
        $alreadyBookedWithTeacher = false;

        if($teacherFilterView->getCurrentTeacher() !== null) {
            $appointments = $this->appointmentRepository->findForTeacher($teacherFilterView->getCurrentTeacher(), $parentsDay);
            $ownAppointments = $this->appointmentRepository->findForStudents($user->getStudents()->toArray(), $parentsDay);

            foreach($ownAppointments as $ownAppointment) {
                foreach($ownAppointment->getTeachers() as $teacher) {
                    if($teacher->getId() === $teacherFilterView->getCurrentTeacher()->getId()) {
                        $alreadyBookedWithTeacher = true;
                    }
                }

                foreach($appointments as $appointment) {
                    if($this->areAppointmentsOverlapping($ownAppointment, $appointment)) {
                        $unavailableAppointments[] = $appointment;
                    }
                }
            }
        }

        $sorter->sort($appointments, ParentsDayAppointmentStrategy::class);

        return $this->render('parents_days/book_overview.html.twig', [
            'appointments' => $appointments,
            'teacherFilter' => $teacherFilterView,
            'parentsDay' => $parentsDay,
            'unavailableAppointments' => $unavailableAppointments,
            'alreadyBookedWithTeacher' => $alreadyBookedWithTeacher,
            'bookForm' => $this->createForm(BookParentsDayAppointmentType::class, null, [ 'user' => $this->getUser()])->createView()
        ]);
    }

    #[Route('/{uuid}/add_appointments', name: 'add_parents_day_appointments')]
    public function addAppointments(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, Request $request, AppointmentsCreator $appointmentsCreator): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::CREATE);

        /** @var User $user */
        $user = $this->getUser();

        $params = new AppointmentsCreatorParams();
        $params->parentsDay = $parentsDay;
        $form = $this->createForm(AppointmentsCreatorParamsType::class, $params);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointmentsCreator->createAppointments($user->getTeacher(), $params);
            $this->addFlash('success', 'parents_day.appointments.add.success');

            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/add.html.twig', [
            'form' => $form->createView(),
            'parentsDay' => $parentsDay
        ]);
    }

    #[Route('/{uuid}/add_appointment', name: 'add_parents_day_appointment')]
    public function addAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::CREATE);

        /** @var User $user */
        $user = $this->getUser();

        $appointment = new ParentsDayAppointment();
        $appointment->setParentsDay($parentsDay);
        if($user->getTeacher() !== null) {
            $appointment->addTeacher($user->getTeacher());
        }

        $form = $this->createForm(ParentsDayAppointmentType::class, $appointment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->appointmentRepository->persist($appointment);
            $this->addFlash('success', 'parents_day.appointments.add.success');

            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/add_single.html.twig', [
            'form' => $form->createView(),
            'parentsDay' => $parentsDay
        ]);
    }

    #[Route('/a/{uuid}/book', name: 'book_parents_day_appointment')]
    public function bookAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::BOOK, $appointment);

        $form = $this->createForm(BookParentsDayAppointmentType::class, $appointment, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $preventCreation = false;

            foreach($this->appointmentRepository->findForStudents($user->getStudents()->toArray(), $appointment->getParentsDay()) as $existingAppointment) {
                if($existingAppointment->getTeachers()->count() === 1 && $appointment->getTeachers()->count() === 1) {
                    if($existingAppointment->getTeachers()->first()->getId() === $appointment->getTeachers()->first()->getId()) {
                        foreach($appointment->getStudents() as $student) {
                            $existingAppointment->removeStudent($student);
                        }

                        $this->appointmentRepository->persist($existingAppointment);
                    }
                } else {
                    $preventCreation = true;
                }
            }

            if($preventCreation === false) {
                $this->appointmentRepository->persist($appointment);
            }

            $this->addFlash('success', 'parents_day.appointments.book.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/book.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/unbook', name: 'unbook_parents_day_appointment')]
    public function unbookAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::UNBOOK, $appointment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'parents_day.appointments.unbook.confirm',
            'csrf_token_id' => 'unbook_appointment'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $existingStudents = $appointment->getStudents();

            foreach($existingStudents as $existingStudent) {
                foreach($user->getStudents() as $student) {
                    if($student->getId() === $existingStudent->getId()) {
                        $appointment->removeStudent($existingStudent);
                    }
                }
            }

            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.unbook.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/unbook.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/assign', name: 'assign_parents_day_appointment')]
    public function assignAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::EDIT, $appointment);

        $form = $this->createForm(ParentsDayAppointmentType::class, $appointment, [ 'only_students' => true]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointment->setIsBlocked(false);
            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.assign.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/assign.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/unassign', name: 'unassign_parents_day_appointment')]
    public function unassignAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::EDIT, $appointment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'parents_day.appointments.unassign.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $appointment->getStudents()->clear();

            $teachers = $appointment->getTeachers()->toArray(); // create a clone to prevent changing collection during iteration
            foreach($teachers as $teacher) {
                if($teacher->getId() !== $user->getTeacher()?->getId()) {
                    $appointment->removeTeacher($teacher);
                }
            }

            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.unassign.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/unassign.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/block', name: 'block_parents_day_appointment')]
    public function blockAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::EDIT, $appointment);

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'parents_day.appointments.block.confirm',
            'message_parameters' => [
                '%start%' => $appointment->getStart()->format('h:i'),
                '%end%' => $appointment->getEnd()->format('h:i')
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointment->setIsBlocked(true);
            $appointment->getStudents()->clear();
            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.block.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/block.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/unblock', name: 'unblock_parents_day_appointment')]
    public function unblockAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::EDIT, $appointment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'parents_day.appointments.unblock.confirm',
            'message_parameters' => [
                '%start%' => $appointment->getStart()->format('h:i'),
                '%end%' => $appointment->getEnd()->format('h:i')
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointment->setIsBlocked(false);

            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.unblock.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/unblock.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/edit', name: 'edit_parents_day_appointment')]
    public function editAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::EDIT, $appointment);

        $form = $this->createForm(ParentsDayAppointmentType::class, $appointment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->appointmentRepository->persist($appointment);
            $this->addFlash('success', 'parents_day.appointments.edit.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/edit.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/remove', name: 'remove_parents_day_appointment')]
    public function removeAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::REMOVE, $appointment);
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'parents_day.appointments.remove.confirm',
            'message_parameters' => [
                '%start%' => $appointment->getStart()->format('h:i'),
                '%end%' => $appointment->getEnd()->format('h:i')
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->appointmentRepository->remove($appointment);
            $this->addFlash('success', 'parents_day.appointments.remove.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/remove.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/a/{uuid}/cancel', name: 'cancel_parents_day_appointment')]
    public function cancelAppointment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDayAppointment $appointment, Request $request): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::CANCEL, $appointment);

        $form = $this->createForm(CancelParentsDayAppointmentType::class, null, [
            'confirm_label' => 'parents_day.appointments.cancel.confirm',
            'csrf_token_id' => 'cancel_appointment'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $appointment->setIsCancelled(true);
            $appointment->setCancelReason($form->get('reason')->getData());
            $appointment->setCancelledBy($user);
            $this->appointmentRepository->persist($appointment);

            $this->addFlash('success', 'parents_day.appointments.cancel.success');
            return $this->redirectToRoute('parents_day');
        }

        return $this->render('parents_days/cancel.html.twig', [
            'form' => $form->createView(),
            'appointment' => $appointment
        ]);
    }

    #[Route('/{uuid}/prepare', name: 'prepare_parents_day')]
    public function prepare(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, TuitionFilter $tuitionFilter, Request $request,
                            ParentsDayParentalInformationRepositoryInterface $repository,
                            SectionResolverInterface $sectionResolver, ParentsDayParentalInformationResolver $informationResolver): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::CREATE);

        /** @var User $user */
        $user = $this->getUser();
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionResolver->getSectionForDate($parentsDay->getDate()), $user, true);

        $information = [ ];
        $form = null;

        if($tuitionFilterView->getCurrentTuition() !== null && $user->getTeacher() !== null) {
            $information['students'] = $informationResolver->findOrCreateForTeacherAndTuition($parentsDay, $user->getTeacher(), $tuitionFilterView->getCurrentTuition());

            $builder = $this->createFormBuilder($information);
            $builder->add('students', CollectionType::class, [
                'entry_type' => ParentsDayParentalInformationType::class,
                'allow_add' => false,
                'allow_delete' => false
            ]);

            $form = $builder->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->beginTransaction();
                foreach ($information['students'] as $information) {
                    $repository->persist($information);
                }
                $repository->commit();

                $this->addFlash('success', 'parents_day.prepare.success');
                return $this->redirectToRoute('prepare_parents_day', [
                    'uuid' => $parentsDay->getUuid(),
                    'tuition' => $tuitionFilterView->getCurrentTuition()->getUuid()
                ]);
            }
        }

        return $this->render('parents_days/prepare.html.twig', [
            'parentsDay' => $parentsDay,
            'tuitionFilter' => $tuitionFilterView,
            'information' => $information,
            'form' => $form?->createView()
        ]);
    }

    #[Route('/{uuid}/cancel_all', name: 'cancel_all_parents_day_appointments')]
    public function cancelAllAppointments(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, TuitionFilter $tuitionFilter, Request $request, DateHelper $dateHelper): Response {
        $this->denyAccessUnlessGranted(ParentsDayAppointmentVoter::CREATE);

        if($parentsDay->getBookingAllowedUntil() >= $dateHelper->getToday()) {
            $this->addFlash('error', 'parents_day.appointments.cancel_all.invalid');
            return $this->redirectToRoute('parents_day');
        }

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CancelParentsDayAppointmentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $appointments = $this->appointmentRepository->findForTeacher($user->getTeacher(), $parentsDay);
            foreach($appointments as $appointment) {
                if($this->isGranted(ParentsDayAppointmentVoter::CANCEL, $appointment)) {
                    $appointment->setIsCancelled(true);
                    $appointment->setCancelReason($form->get('reason')->getData());
                    $appointment->setCancelledBy($user);
                    $this->appointmentRepository->persist($appointment);
                }
            }

            $this->addFlash('success', 'parents_day.appointments.cancel_all.success');
            return $this->redirectToRoute('parents_day', [
                'day' => $parentsDay->getUuid()
            ]);
        }

        return $this->render('parents_days/cancel_all.html.twig', [
            'parentsDay' => $parentsDay,
            'form' => $form->createView()
        ]);
    }

    private function areAppointmentsOverlapping(ParentsDayAppointment $appointmentA, ParentsDayAppointment $appointmentB): bool {
        return $appointmentA->getStart() < $appointmentB->getEnd() && $appointmentB->getStart() < $appointmentA->getEnd();
    }
}
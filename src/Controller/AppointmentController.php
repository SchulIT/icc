<?php

namespace App\Controller;

use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\DeviceToken;
use App\Entity\DeviceTokenType;
use App\Entity\MessageScope;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\AppointmentIcsExporter;
use App\Form\DeviceTokenType as DeviceTokenTypeForm;
use App\Grouping\AppointmentDateStrategy;
use App\Grouping\Grouper;
use App\Repository\AppointmentRepositoryInterface;
use App\Security\Devices\DeviceManager;
use App\Security\Voter\AppointmentVoter;
use App\Sorting\AppointmentDateGroupStrategy;
use App\Sorting\AppointmentDateStrategy as AppointmentDateSortingStrategy;
use App\Sorting\Sorter;
use App\Utils\ColorUtils;
use App\View\Filter\AppointmentCategoriesFilter;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/appointments")
 */
class AppointmentController extends AbstractControllerWithMessages {

    /**
     * @Route("", name="appointments")
     */
    public function index(AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter, TeacherFilter $teacherFilter) {
        /** @var User $user */
        $user = $this->getUser();

        $categoryFilterView = $categoryFilter->handle([ ]);
        $studentFilterView = $studentFilter->handle(null, $user);
        $studyGroupView = $studyGroupFilter->handle(null, $user);
        $teacherFilterView = $teacherFilter->handle(null, $user);

        return $this->renderWithMessages('appointments/index.html.twig', [
            'categoryFilter' => $categoryFilterView,
            'studentFilter' => $studentFilterView,
            'studyGroupFilter' => $studyGroupView,
            'teacherFilter' => $teacherFilterView
        ]);
    }

    /**
     * @Route("/xhr", name="appointments_xhr", methods={"GET"})
     */
    public function indexXhr(AppointmentRepositoryInterface $appointmentRepository, ColorUtils $colorUtils, TranslatorInterface $translator,
                             StudyGroupsGradeStringConverter $studyGroupsGradeStringConverter, TeacherStringConverter $teacherStringConverter,
                             AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter, TeacherFilter $teacherFilter, Request $request,
                             ?int $studentId = null, ?int $studyGroupId = null, ?string $teacherAcronym = null, ?string $query = null, ?bool $showAll = false) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        $categoryFilterView = $categoryFilter->handle(explode(',', $request->query->get('categoryIds', '')));
        $studentFilterView = $studentFilter->handle($studentId, $user);
        $studyGroupView = $studyGroupFilter->handle($studyGroupId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user);

        $appointments = [ ];
        $today = null;

        if($studentFilterView->getCurrentStudent() !== null) {
            $appointments = $appointmentRepository->findAllForStudents([$studentFilterView->getCurrentStudent()], $today);
        } else if($studyGroupView->getCurrentStudyGroup() !== null) {
            $appointments = $appointmentRepository->findAllForStudyGroup($studyGroupView->getCurrentStudyGroup(), $today);
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $appointments = $appointmentRepository->findAllForTeacher($teacherFilterView->getCurrentTeacher(), $today);
        } else {
            if($isStudent || $isParent) {
                $appointments = $appointmentRepository->findAllForStudents($user->getStudents()->toArray(), $today);
            } else {
                $appointments = $appointmentRepository->findAll([ ], null, $today);
            }
        }

        if(!empty($categoryFilterView->getCurrentCategories())) {
            $selectedCategoryIds = array_map(function(AppointmentCategory $category) {
                return $category->getId();
            }, $categoryFilterView->getCurrentCategories());

            $appointments = array_filter($appointments, function(Appointment $appointment) use($selectedCategoryIds) {
                return in_array($appointment->getCategory()->getId(), $selectedCategoryIds);
            });
        }

        $json = [ ];

        foreach($appointments as $appointment) {
            if($this->isGranted(AppointmentVoter::View, $appointment) !== true) {
                continue;
            }

            $view = [
                [
                    'label' => $translator->trans('label.start'),
                    'content' => $appointment->getStart()->format($translator->trans($appointment->isAllDay() ? 'date.format' : 'date.with_time'))
                ],
                [
                    'label' => $translator->trans('label.end'),
                    'content' => $appointment->getEnd()->format($translator->trans($appointment->isAllDay() ? 'date.format' : 'date.with_time'))
                ]
            ];

            if(!empty($appointment->getLocation())) {
                $view[] = [
                    'label' => $translator->trans('label.location'),
                    'content' => $appointment->getLocation()
                ];
            }

            if($appointment->getStudyGroups()->count() > 0) {
                $view[] = [
                    'label' => $translator->trans('label.study_groups', ['%count%' => $appointment->getStudyGroups()->count()]),
                    'content' => $studyGroupsGradeStringConverter->convert($appointment->getStudyGroups())
                ];
            }

            if($appointment->getOrganizers()->count() > 0) {
                $view[] = [
                    'label' => $translator->trans('label.organizers'),
                    'content' => implode(', ', array_map(function (Teacher $teacher) use ($teacherStringConverter) {
                        return $teacherStringConverter->convert($teacher);
                    }, $appointment->getOrganizers()->toArray()))
                ];
            }

            if(!empty($appointment->getExternalOrganizers())) {
                $view[] = [
                    'label' => $translator->trans('label.external_organizers'),
                    'content' => $appointment->getExternalOrganizers()
                ];
            }

            $json[] = [
                'id' => $appointment->getId(),
                'allDay' => $appointment->isAllDay(),
                'title' => $appointment->getTitle(),
                'textColor' => $colorUtils->getForeground($appointment->getCategory()->getColor()),
                'backgroundColor' => $appointment->getCategory()->getColor(),
                'start' => $appointment->getStart()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'end' => $appointment->getEnd()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'extendedProps' => [
                    'category'=> $appointment->getCategory()->getName(),
                    'content' => $appointment->getContent(),
                    'view' => $view
                ]
            ];
        }

        return $this->json($json);
    }

    /**
     * @Route("/export", name="appointments_export")
     */
    public function export(Request $request, DeviceManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new DeviceToken())
            ->setType(DeviceTokenType::Calendar())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistDeviceToken($deviceToken);
        }

        return $this->renderWithMessages('appointments/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    /**
     * @Route("/ics/download", name="appointments_ics")
     * @Route("/ics/download/{token}", name="appointments_ics_token")
     */
    public function ics(AppointmentIcsExporter $exporter) {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Appointments();
    }
}
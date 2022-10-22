<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\TeacherStringConverter;
use App\Converter\UserStringConverter;
use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\MessageScope;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\AppointmentIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Security\Voter\AppointmentVoter;
use App\Security\Voter\ExamVoter;
use App\Settings\AppointmentsSettings;
use App\Settings\TimetableSettings;
use App\Timetable\TimetableTimeHelper;
use App\Utils\ArrayUtils;
use App\Utils\ColorUtils;
use App\View\Filter\AppointmentCategoriesFilter;
use App\View\Filter\GradesFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use DateInterval;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/appointments')]
class AppointmentController extends AbstractControllerWithMessages {

    #[Route(path: '', name: 'appointments')]
    public function index(SectionFilter $sectionFilter, AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter,
                          TeacherFilter $teacherFilter, GradesFilter $gradesFilter, Request $request, ImportDateTypeRepositoryInterface $importDateTypeRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        $categoryFilterView = $categoryFilter->handle([ ]);
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle(null, $sectionFilterView->getCurrentSection(), $user);
        $studyGroupView = $studyGroupFilter->handle(null, $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle(null, $sectionFilterView->getCurrentSection(), $user, false);
        $gradesFilterView = $gradesFilter->handle([], $sectionFilterView->getCurrentSection(), $user);

        return $this->renderWithMessages('appointments/index.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'categoryFilter' => $categoryFilterView,
            'studentFilter' => $studentFilterView,
            'studyGroupFilter' => $studyGroupView,
            'teacherFilter' => $teacherFilterView,
            'examGradesFilter' => $gradesFilterView,
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Appointment::class)
        ]);
    }

    #[Route(path: '/xhr', name: 'appointments_xhr', methods: ['GET'])]
    public function indexXhr(AppointmentRepositoryInterface $appointmentRepository, ColorUtils $colorUtils, TranslatorInterface $translator,
                             StudyGroupsGradeStringConverter $studyGroupsGradeStringConverter, TeacherStringConverter $teacherStringConverter,
                             AppointmentCategoriesFilter $categoryFilter, SectionFilter $sectionFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter,
                             TeacherFilter $teacherFilter, GradesFilter $gradesFilter, ExamRepositoryInterface $examRepository,
                             AppointmentsSettings $appointmentsSettings, TimetableTimeHelper $timetableTimeHelper, UserStringConverter $userStringConverter, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();
        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $showAll = $request->query->getBoolean('all', false);
        $categoryFilterView = $categoryFilter->handle(explode(',', $request->query->get('categories', '')));
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $studyGroupView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $studentFilterView->getCurrentStudent() === null && $studyGroupView->getCurrentStudyGroup() === null);
        $examGradesFilterView = $gradesFilter->handle(explode(',', $request->query->get('exam_grades', '')), $sectionFilterView->getCurrentSection(), $user);

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
            $selectedCategoryIds = array_map(fn(AppointmentCategory $category) => $category->getId(), $categoryFilterView->getCurrentCategories());

            $appointments = array_filter($appointments, fn(Appointment $appointment) => in_array($appointment->getCategory()->getId(), $selectedCategoryIds));
        }

        $json = [ ];

        foreach($appointments as $appointment) {
            if($this->isGranted(AppointmentVoter::View, $appointment) !== true) {
                continue;
            }

            if($appointment->isAllDay() && $appointment->getDuration()->d === 1) {
                $view = [
                    [
                        'label' => $translator->trans('label.date'),
                        'content' => $appointment->getStart()->format($translator->trans('date.format'))
                    ]
                ];
            } else {
                $view = [
                    [
                        'label' => $translator->trans('label.start'),
                        'content' => $appointment->getStart()->format($translator->trans($appointment->isAllDay() ? 'date.format' : 'date.with_time'))
                    ],
                    [
                        'label' => $translator->trans('label.end'),
                        'content' => $appointment->getRealEnd()->format($translator->trans($appointment->isAllDay() ? 'date.format' : 'date.with_time'))
                    ]
                ];
            }

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
                    'content' => implode(', ', array_map(fn(Teacher $teacher) => $teacherStringConverter->convert($teacher), $appointment->getOrganizers()->toArray()))
                ];
            }

            if(!empty($appointment->getExternalOrganizers())) {
                $view[] = [
                    'label' => $translator->trans('label.external_organizers'),
                    'content' => $appointment->getExternalOrganizers()
                ];
            }

            if($appointment->getCreatedBy() !== null) {
                $view[] = [
                    'label' => $translator->trans('label.created_by'),
                    'content' => $userStringConverter->convert($appointment->getCreatedBy(), false)
                ];
            }

            $json[] = [
                'uuid' => $appointment->getUuid(),
                'allDay' => $appointment->isAllDay(),
                'title' => ($appointment->isConfirmed() === false ? '(✗) ' : '') . $appointment->getTitle(),
                'textColor' => $colorUtils->getForeground($appointment->getCategory()->getColor()),
                'backgroundColor' => $appointment->getCategory()->getColor(),
                'start' => $appointment->getStart()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'end' => $appointment->getEnd()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'extendedProps' => [
                    'category'=> $appointment->getCategory()->getName(),
                    'content' => $appointment->getContent(),
                    'view' => $view,
                    'confirmation_status' => $appointment->isConfirmed() === false ? $translator->trans('label.not_confirmed') : null
                ]
            ];
        }

        // Add exams
        if((is_countable($examGradesFilterView->getCurrentGrades()) ? count($examGradesFilterView->getCurrentGrades()) : 0) > 0) {
            $exams = [ ];

            foreach($examGradesFilterView->getCurrentGrades() as $grade) {
                $exams = array_merge($exams, $examRepository->findAllByGrade($grade));
            }

            $exams = ArrayUtils::createArrayWithKeys($exams, fn(Exam $exam) => $exam->getUuid()->toString());

            /** @var Exam $exam */
            foreach($exams as $exam) {
                if($this->isGranted(ExamVoter::Show, $exam)) {
                    $view = [ ];

                    $tuitions = implode(', ', $exam->getTuitions()->map(fn(Tuition $tuition) => $tuition->getName())->toArray());

                    $view[] = [
                        'label' => $translator->trans('label.tuitions'),
                        'content' => $tuitions
                    ];

                    $grades = [ ];
                    $teachers = [ ];

                    /** @var Tuition $tuition */
                    foreach($exam->getTuitions() as $tuition) {
                        /** @var Grade $grade */
                        foreach($tuition->getStudyGroup()->getGrades() as $grade) {
                            $grades[] = $grade->getName();
                        }

                        /** @var Teacher $teacher */
                        foreach($tuition->getTeachers() as $teacher) {
                            $teachers[] = $teacherStringConverter->convert($teacher);
                        }

                        $grades = array_unique($grades);
                        $teachers = array_unique($teachers);
                    }

                    $view[] = [
                        'label' => $translator->trans('label.teacher'),
                        'content' => $teachers
                    ];

                    $view[] = [
                        'label' => $translator->trans('label.grades'),
                        'content' => implode(', ', $grades)
                    ];

                    if($exam->getRoom() !== null) {
                        $view[] = [
                            'label' => $translator->trans('label.room'),
                            'content' => $exam->getRoom()->getName()
                        ];
                    }

                    $view[] = [
                        'label' => $translator->trans('plans.exams.time'),
                        'content' => $translator->trans('label.exam_lessons', [
                            '%start%' => $exam->getLessonStart(),
                            '%end%' => $exam->getLessonEnd(),
                            '%count%' => $exam->getLessonEnd() - $exam->getLessonStart()
                        ])
                    ];

                    $json[] = [
                        'uuid' => $exam->getUuid(),
                        'allDay' => false,
                        'title' => sprintf('%s: %s', implode(', ', $grades), $tuitions),
                        'textColor' => empty($appointmentsSettings->getExamColor()) ? '#000000' : $colorUtils->getForeground($appointmentsSettings->getExamColor()),
                        'backgroundColor' => empty($appointmentsSettings->getExamColor()) ? '#ffffff' : $appointmentsSettings->getExamColor(),
                        'start' => $timetableTimeHelper->getLessonStartDateTime($exam->getDate(), $exam->getLessonStart())->format('Y-m-d H:i'),
                        'end' => $timetableTimeHelper->getLessonEndDateTime($exam->getDate(), $exam->getLessonEnd())->format('Y-m-d H:i'),
                        'extendedProps' => [
                            'category' => $translator->trans('plans.exams.label'),
                            'content' => $exam->getDescription(),
                            'view' => $view
                        ]
                    ];
                }
            }
        }

        return $this->json($json);
    }

    #[Route(path: '/export', name: 'appointments_export')]
    public function export(Request $request, IcsAccessTokenManager $manager): Response {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Calendar())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistToken($deviceToken);
        }

        return $this->renderWithMessages('appointments/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    #[Route(path: '/ics/download', name: 'appointments_ics')]
    #[Route(path: '/ics/download/{token}', name: 'appointments_ics_token')]
    public function ics(AppointmentIcsExporter $exporter): Response {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Appointments();
    }
}
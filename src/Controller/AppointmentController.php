<?php

namespace App\Controller;

use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\TeacherStringConverter;
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
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use DateInterval;
use DateTime;
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
    public function index(AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter,
                          TeacherFilter $teacherFilter, GradesFilter $gradesFilter, ImportDateTypeRepositoryInterface $importDateTypeRepository) {
        /** @var User $user */
        $user = $this->getUser();

        $categoryFilterView = $categoryFilter->handle([ ]);
        $studentFilterView = $studentFilter->handle(null, $user);
        $studyGroupView = $studyGroupFilter->handle(null, $user);
        $teacherFilterView = $teacherFilter->handle(null, $user, false);
        $gradesFilterView = $gradesFilter->handle([], $user);

        return $this->renderWithMessages('appointments/index.html.twig', [
            'categoryFilter' => $categoryFilterView,
            'studentFilter' => $studentFilterView,
            'studyGroupFilter' => $studyGroupView,
            'teacherFilter' => $teacherFilterView,
            'examGradesFilter' => $gradesFilterView,
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Appointment::class)
        ]);
    }

    /**
     * @Route("/xhr", name="appointments_xhr", methods={"GET"})
     */
    public function indexXhr(AppointmentRepositoryInterface $appointmentRepository, ColorUtils $colorUtils, TranslatorInterface $translator,
                             StudyGroupsGradeStringConverter $studyGroupsGradeStringConverter, TeacherStringConverter $teacherStringConverter,
                             AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, StudyGroupFilter $studyGroupFilter,
                             TeacherFilter $teacherFilter, GradesFilter $gradesFilter, ExamRepositoryInterface $examRepository,
                             AppointmentsSettings $appointmentsSettings, TimetableTimeHelper $timetableTimeHelper, Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        $showAll = $request->query->getBoolean('all', false);
        $categoryFilterView = $categoryFilter->handle(explode(',', $request->query->get('categories', '')));
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user);
        $studyGroupView = $studyGroupFilter->handle($request->query->get('study_group', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $studentFilterView->getCurrentStudent() === null && $studyGroupView->getCurrentStudyGroup() === null);
        $examGradesFilterView = $gradesFilter->handle(explode(',', $request->query->get('exam_grades', '')), $user);

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
                'uuid' => $appointment->getUuid(),
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

        // Add exams
        if(count($examGradesFilterView->getCurrentGrades()) > 0) {
            $exams = [ ];

            foreach($examGradesFilterView->getCurrentGrades() as $grade) {
                $exams = array_merge($exams, $examRepository->findAllByGrade($grade));
            }

            $exams = ArrayUtils::createArrayWithKeys($exams, function(Exam $exam) {
                return $exam->getUuid()->toString();
            });

            /** @var Exam $exam */
            foreach($exams as $exam) {
                if($this->isGranted(ExamVoter::Show, $exam)) {
                    $view = [ ];

                    $tuitions = implode(', ', $exam->getTuitions()->map(function(Tuition $tuition) { return $tuition->getName(); })->toArray());

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

                    if(count($exam->getRooms()) > 0) {
                        $view[] = [
                            'label' => $translator->trans('label.room'),
                            'content' => $exam->getRooms()[0]
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

    /**
     * @Route("/export", name="appointments_export")
     */
    public function export(Request $request, IcsAccessTokenManager $manager) {
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
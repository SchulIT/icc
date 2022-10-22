<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Book\Export\BookExporter;
use App\Book\Lesson;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\Student\StudentInfo;
use App\Book\Student\StudentInfoResolver;
use App\Entity\DateLesson;
use App\Entity\ExcuseNote;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceType;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\GenericDateStrategy;
use App\Grouping\Grouper;
use App\Grouping\LessonAttendanceCommentsGroup;
use App\Grouping\LessonDayStrategy;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\LessonEntryVoter;
use App\Settings\BookSettings;
use App\Sorting\LessonAttendanceGroupStrategy;
use App\Sorting\LessonAttendanceStrategy;
use App\Sorting\LessonDayGroupStrategy;
use App\Sorting\LessonStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentAwareGradeFilter;
use App\View\Filter\StudentAwareTuitionFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\TuitionFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController {

    use CalendarWeeksTrait;

    private const ItemsPerPage = 25;

    private function getClosestMonthStart(DateTime $dateTime): DateTime {
        $dateTime = clone $dateTime;
        $dateTime->setDate((int)$dateTime->format('Y'), (int)$dateTime->format('m'), 1);
        return $dateTime;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end $end - $start must not be greater than one year!
     * @return DateTime[] All first days of the month with their month number as key.
     */
    private function listCalendarMonths(DateTime $start, DateTime $end): array {
        $firstDays = [ ];
        $current = $this->getClosestMonthStart($start);

        while($current < $end) {
            $firstDays[(int)$current->format('m')] = clone $current;
            $current = $current->modify('+1 month');
        }
        return $firstDays;
    }

    private function resolveSelectedDateForTuitionView(Request $request, ?Section $currentSection, DateHelper $dateHelper): ?DateTime {
        $selectedDate = null;
        try {
            if($request->query->has('date')) {
                $selectedDate = new DateTime($request->query->get('date', null));
                $selectedDate->setTime(0, 0, 0);

                $selectedDate = $this->getClosestMonthStart($selectedDate);
            }
        } catch (Exception $e) {
            $selectedDate = null;
        }

        if($selectedDate === null && $currentSection !== null) {
            $selectedDate = $this->getClosestMonthStart($dateHelper->getToday());
        }

        if($selectedDate !== null && $currentSection !== null && $dateHelper->isBetween($selectedDate, $currentSection->getStart(), $currentSection->getEnd()) !== true) {
            $selectedDate = $this->getClosestMonthStart($currentSection->getEnd());
        }

        return $selectedDate;
    }

    /**
     * @param Section|null $currentSection
     * @param User $user
     * @param TuitionRepositoryInterface $tuitionRepository
     * @return Tuition[]
     */
    private function resolveOwnTuitions(?Section $currentSection, User $user, TuitionRepositoryInterface $tuitionRepository): array {
        if($currentSection === null) {
            return [ ];
        }

        if (EnumArrayUtils::inArray($user->getUserType(), [UserType::Student(), UserType::Parent()])) {
            return $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $currentSection);
        } else if ($user->getUserType()->equals(UserType::Teacher())) {
            return $tuitionRepository->findAllByTeacher($user->getTeacher(), $currentSection);
        }

        return [ ];
    }

    /**
     * @param Section|null $currentSection
     * @param User $user
     * @return Grade[]
     */
    private function resolveOwnGrades(?Section $currentSection, User $user): array {
        if($currentSection === null) {
            return [ ];
        }

        if (EnumArrayUtils::inArray($user->getUserType(), [UserType::Student(), UserType::Parent()])) {
            return ArrayUtils::unique(
                $user->getStudents()->map(function(Student $student) use($currentSection) {
                    return $student->getGrade($currentSection);
                })
            );
        } else if ($user->getUserType()->equals(UserType::Teacher())) {
            return $user->getTeacher()->getGrades()->
                filter(function(GradeTeacher $gradeTeacher) use ($currentSection) {
                    return $gradeTeacher->getSection() === $currentSection;
                })
                ->map(function(GradeTeacher $gradeTeacher) {
                    return $gradeTeacher->getGrade();
                })
                ->toArray();
        }

        return [ ];
    }

    /**
     * @Route("/entry", name="book")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter, TeacherFilter $teacherFilter,
                          TuitionRepositoryInterface $tuitionRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository, DateHelper $dateHelper, Request $request,
                          EntryOverviewHelper $entryOverviewHelper, AbsenceExcuseResolver $absenceExcuseResolver, BookSettings $settings) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $selectedDate = $this->resolveSelectedDate($request, $sectionFilterView->getCurrentSection(), $dateHelper);

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $selectedDate = $this->resolveSelectedDateForTuitionView($request, $sectionFilterView->getCurrentSection(), $dateHelper);
        }

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        // Lessons / Entries
        $overview = null;
        $overallOverview = null;
        $missingExcuseCount = 0;
        $info = [ ];

        if($selectedDate !== null) {
            if ($gradeFilterView->getCurrentGrade() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForGrade($gradeFilterView->getCurrentGrade(), $selectedDate, (clone $selectedDate)->modify('+6 days'));

                $students = $gradeFilterView->getCurrentGrade()->getMemberships()->filter(function(GradeMembership $membership) use ($sectionFilterView) {
                    return $membership->getSection()->getId() === $sectionFilterView->getCurrentSection()->getId();
                })->map(function(GradeMembership $membership) {
                    return $membership->getStudent();
                })->toArray();

                foreach($students as $student) {
                    $info[] = $absenceExcuseResolver->resolve($student);
                }
            } else if ($tuitionFilterView->getCurrentTuition() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition(), $selectedDate, (clone $selectedDate)->modify('+1 month')->modify('-1 day'));

                $students = $tuitionFilterView->getCurrentTuition()->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                });

                foreach($students as $student) {
                    $info[] = $absenceExcuseResolver->resolve($student, [$tuitionFilterView->getCurrentTuition()]);
                }
            } else if($teacherFilterView->getCurrentTeacher() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate, (clone $selectedDate)->modify('+6 days'));
                $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());

                // IDs of already handled students
                $studentIds = [ ];

                foreach($tuitions as $tuition) {
                    /** @var StudyGroupMembership $membership */
                    foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                        $student = $membership->getStudent();

                        if(in_array($student->getId(), $studentIds)) {
                            continue;
                        }

                        $info[] = $absenceExcuseResolver->resolve($student, $tuitions);
                        $studentIds[] = $student->getId();
                    }
                }
            }
        }

        $teacherGrades = [ ];
        if($teacherFilterView->getCurrentTeacher() !== null) {
            $teacherGrades = $teacherFilterView->getCurrentTeacher()->getGrades()
                ->filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection()->getId() === $sectionFilterView->getCurrentSection()->getId())
                ->map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getGrade()->getId())
                ->toArray();
        }

        $missingExcuses = array_filter($info, function(StudentInfo $info) use ($teacherFilterView, $teacherGrades, $sectionFilterView, $settings) {
            if($teacherFilterView->getCurrentTeacher() === null) {
                return $info->getNotExcusedOrNotSetLessonsCount() > 0;
            }

            // When filter view is active, check settings first
            $studentGrade = $info->getStudent()->getGrade($sectionFilterView->getCurrentSection())?->getId();

            if($studentGrade === null) {
                // fallback (this should not happen)
                return $info->getNotExcusedOrNotSetLessonsCount() > 0;
            }

            $isStudentInTeacherGrade = count(array_intersect([$studentGrade], $teacherGrades)) > 0;

            if(in_array($studentGrade, $settings->getGradesGradeTeacherExcuses()) && $isStudentInTeacherGrade) {
                return $info->getNotExcusedOrNotSetLessonsCount() > 0;
            }

            if(in_array($studentGrade, $settings->getGradesTuitionTeacherExcuses()) && $isStudentInTeacherGrade !== true) {
                return $info->getNotExcusedOrNotSetLessonsCount() > 0;
            }

            return false;
        });
        $missingExcuseCount = array_sum(
            array_map(function(StudentInfo $info) {
                return $info->getNotExcusedOrNotSetLessonsCount();
            }, $missingExcuses));

        $weekStarts = [ ];
        $monthStarts = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
            $monthStarts = $this->listCalendarMonths($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        $lateStudentsByLesson = [ ];
        $absentStudentsByLesson = [ ];

        foreach($overview->getDays() as $day) {
            $excusesByStudent = [ ];

            foreach($day->getLessons() as $lesson) {
                if($lesson->getEntry() === null) {
                    continue;
                }

                $uuid = $lesson->getEntry()->getUuid()->toString();
                $lateStudentsByLesson[$uuid] = [];
                $absentStudentsByLesson[$uuid] = [];

                if($lesson->getAbsentCount() === 0 && $lesson->getLateCount() === 0) {
                    continue;
                }

                /** @var LessonAttendance $attendance */
                foreach($lesson->getEntry()->getAttendances() as $attendance) {
                    if($attendance->getType() === LessonAttendanceType::Late) {
                        $lateStudentsByLesson[$uuid][] = $attendance;
                    } else if($attendance->getType() === LessonAttendanceType::Absent) {
                        $studentUuid = $attendance->getStudent()->getUuid()->toString();

                        if(!isset($excusesByStudent[$studentUuid])) {
                            $excusesByStudent[$studentUuid] = $excuseNoteRepository->findByStudentsAndDate([$attendance->getStudent()], $day->getDate());
                        }

                        /** @var ExcuseNote $excuseNote */
                        foreach($excusesByStudent[$studentUuid] as $excuseNote) {
                            if($attendance->getExcuseStatus() === LessonAttendanceExcuseStatus::NotSet && (new DateLesson())->setDate($day->getDate())->setLesson($lesson->getLessonNumber())->isBetween($excuseNote->getFrom(), $excuseNote->getUntil())) {
                                $attendance->setExcuseStatus(LessonAttendanceExcuseStatus::Excused);
                            }
                        }

                        $absentStudentsByLesson[$uuid][] = $attendance;
                    }
                }
            }
        }

        return $this->render('books/index.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'selectedDate' => $selectedDate,
            'overview' => $overview,
            'weekStarts' => $weekStarts,
            'monthStarts' => $monthStarts,
            'missingExcuses' => $missingExcuses,
            'missingExcusesCount' => $missingExcuseCount,
            'absentStudentsByLesson' => $absentStudentsByLesson,
            'lateStudentsByLesson' => $lateStudentsByLesson
        ]);
    }

    /**
     * @Route("/missing", name="missing_book_entries")
     */
    public function missing(Request $request, SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter,
                            TuitionFilter $tuitionFilter, TimetableLessonRepositoryInterface $lessonRepository, TuitionRepositoryInterface $tuitionRepository,
                            DateHelper $dateHelper, Sorter $sorter, Grouper $grouper) {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $section = $sectionFilterView->getCurrentSection();
        $page = $request->query->getInt('page', 1);
        $paginator = null;

        if($section !== null) {
            $start = $section->getStart();
            $end = $dateHelper->getToday();

            if($gradeFilterView->getCurrentGrade() !== null) {
                $paginator = $lessonRepository->getMissingByGradePaginator(self::ItemsPerPage, $page, $gradeFilterView->getCurrentGrade(), $start, $end);
            } elseif($tuitionFilterView->getCurrentTuition() !== null) {
                $paginator = $lessonRepository->getMissingByTuitionPaginator(self::ItemsPerPage, $page, $tuitionFilterView->getCurrentTuition(), $start, $end);
            } else if($teacherFilterView->getCurrentTeacher() !== null) {
                $paginator = $lessonRepository->getMissingByTeacherPaginator(self::ItemsPerPage, $page, $teacherFilterView->getCurrentTeacher(), $start, $end);
            }
        }

        $missing = [ ];
        $pages = 0;

        if($paginator !== null) {
            $missing = [ ];
            $pages = ceil((float)$paginator->count() / self::ItemsPerPage);

            /** @var TimetableLesson $lessonEntity */
            foreach($paginator->getIterator() as $lessonEntity) {
                for($lessonNumber = $lessonEntity->getLessonStart(); $lessonNumber <= $lessonEntity->getLessonEnd(); $lessonNumber++) {
                    $missing[] = new Lesson(clone $lessonEntity->getDate(), $lessonNumber, $lessonEntity, null);
                }
            }
        }

        $groups = $grouper->group($missing, LessonDayStrategy::class);
        $sorter->sort($groups, LessonDayGroupStrategy::class, SortDirection::Descending());
        $sorter->sortGroupItems($groups, LessonStrategy::class);

        $ownGradesMissingCounts = [];
        $ownTuitionsMissingCounts = [];

        if($section !== null) {
            foreach ($ownGrades as $ownGrade) {
                $ownGradesMissingCounts[$ownGrade->getId()] = $lessonRepository->countMissingByGrade($ownGrade, $start, $end);
            }

            foreach ($ownTuitions as $ownTuition) {
                $ownTuitionsMissingCounts[$ownTuition->getId()] = $lessonRepository->countMissingByTuition($ownTuition, $start, $end);
            }
        }

        return $this->render('books/missing.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'ownGradesMissingCounts' => $ownGradesMissingCounts,
            'ownTuitionsMissingCounts' => $ownTuitionsMissingCounts,
            'groups' => $groups,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/student", name="book_students")
     */
    public function students(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter, TeacherFilter $teacherFilter,
                             TuitionRepositoryInterface $tuitionRepository, StudentRepositoryInterface $studentRepository, StudentInfoResolver $studentInfoResolver,
                             Sorter $sorter, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $students = [ ];
        $tuitions = [ ];
        if($gradeFilterView->getCurrentGrade() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()]);
            $students = $studentRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection());
        } else if($tuitionFilterView->getCurrentTuition() !== null) {
            $tuitions = [ $tuitionFilterView->getCurrentTuition() ];
            $students = $studentRepository->findAllByStudyGroups([$tuitionFilterView->getCurrentTuition()->getStudyGroup()]);
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
            $studyGroups = array_map(function(Tuition $tuition) {
                return $tuition->getStudyGroup();
            }, $tuitions);

            $students = $studentRepository->findAllByStudyGroups($studyGroups);
        }

        $sorter->sort($students, StudentStrategy::class);
        $info = [ ];

        foreach($students as $student) {
            $info[] = $studentInfoResolver->resolveStudentInfo($student, $sectionFilterView->getCurrentSection(), $tuitions);
        }

        return $this->render('books/students.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'info' => $info
        ]);
    }

    /**
     * @Route("/student/{student}", name="book_student")
     * @ParamConverter("student", class="App\Entity\Student", options={"mapping": {"student": "uuid"}})
     */
    public function student(Student $student, SectionFilter $sectionFilter, StudentAwareTuitionFilter $tuitionFilter,
                            StudentAwareGradeFilter $gradeFilter, TeacherFilter  $teacherFilter, Request $request,
                            StudentInfoResolver $infoResolver, TuitionRepositoryInterface $tuitionRepository,
                            Sorter $sorter, Grouper $grouper) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $student);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $student);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $tuitions = [ ];

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $tuitions[] = $tuitionFilterView->getCurrentTuition();
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
        }

        $info = $infoResolver->resolveStudentInfo($student, $sectionFilterView->getCurrentSection(), $tuitions);
        $groups = $grouper->group(
            array_merge(
                $info->getAbsentLessonAttendances(),
                $info->getLateLessonAttendances(),
                $info->getComments()
            ), GenericDateStrategy::class, [
                'group_class' => LessonAttendanceCommentsGroup::class
        ]);

        $sorter->sort($groups, LessonAttendanceGroupStrategy::class, SortDirection::Descending());
        $sorter->sortGroupItems($groups, LessonAttendanceStrategy::class);

        return $this->render('books/student.html.twig', [
            'student' => $student,
            'info' => $info,
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
        ]);
    }

    private function createResponse(string $content, string $contentType, string $filename): Response {
        $response = new Response($content);
        $response->headers->set('Content-Type', $contentType . '; charset=UTF-8');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, transliterator_transliterate('Latin-ASCII', $filename)));

        return $response;
    }

    /**
     * @Route("/{section}/t/{tuition}/export/json", name="book_export_tuition_json")
     * @ParamConverter("section", class="App\Entity\Section", options={"mapping": {"section": "uuid"}})
     * @ParamConverter("tuition", class="App\Entity\Tuition", options={"mapping": {"tuition": "uuid"}})
     */
    public function exportTutionJson(Tuition $tuition, Section $section, BookExporter $exporter) {
        $filename = sprintf('%s-%d-%d.json', $tuition->getName(), $section->getYear(), $section->getNumber());
        $json = $exporter->exportTuitionJson($tuition, $section);
        return $this->createResponse($json, 'application/json', $filename);
    }

    /**
     * @Route("/{section}/t/{tuition}/export/xml", name="book_export_tuition_xml")
     * @ParamConverter("section", class="App\Entity\Section", options={"mapping": {"section": "uuid"}})
     * @ParamConverter("tuition", class="App\Entity\Tuition", options={"mapping": {"tuition": "uuid"}})
     */
    public function exportTuitionXml(Tuition $tuition, Section $section, BookExporter $exporter) {
        $filename = sprintf('%s-%d-%d.xml', $tuition->getName(), $section->getYear(), $section->getNumber());
        $xml = $exporter->exportTuitionXml($tuition, $section);
        return $this->createResponse($xml, 'application/xml', $filename);
    }

    /**
     * @Route("/{section}/g/{grade}/export/json", name="book_export_grade_json")
     * @ParamConverter("section", class="App\Entity\Section", options={"mapping": {"section": "uuid"}})
     * @ParamConverter("grade", class="App\Entity\Grade", options={"mapping": {"grade": "uuid"}})
     */
    public function exportGradeJson(Grade $grade, Section $section, BookExporter $exporter) {
        $filename = sprintf('%s-%d-%d.json', $grade->getName(), $section->getYear(), $section->getNumber());
        $json = $exporter->exportGradeJson($grade, $section);
        return $this->createResponse($json, 'application/json', $filename);
    }

    /**
     * @Route("/{section}/g/{grade}/export/xml", name="book_export_grade_xml")
     * @ParamConverter("section", class="App\Entity\Section", options={"mapping": {"section": "uuid"}})
     * @ParamConverter("grade", class="App\Entity\Grade", options={"mapping": {"grade": "uuid"}})
     */
    public function exportGradeXml(Grade $grade, Section $section, BookExporter $exporter) {
        $filename = sprintf('%s-%d-%d.xml', $grade->getName(), $section->getYear(), $section->getNumber());
        $xml = $exporter->exportGradeXml($grade, $section);
        return $this->createResponse($xml, 'application/xml', $filename);
    }
}
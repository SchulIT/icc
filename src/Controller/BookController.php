<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Book\Export\BookExporter;
use App\Book\IntegrityCheck\CachedIntegrityCheckRunner;
use App\Book\IntegrityCheck\IntegrityCheckRunner;
use App\Book\IntegrityCheck\IntegrityCheckTeacherFilter;
use App\Book\IntegrityCheck\Persistence\ViolationsResolver;
use App\Book\Lesson;
use App\Book\Statistics\BookLessonCountGenerator;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\Student\Cache\CacheWarmupHelper;
use App\Book\Student\Cache\GenerateStudentInfoCountsMessage;
use App\Book\Student\Cache\StudentInfoCountsGenerator;
use App\Book\Student\StudentInfo;
use App\Book\Student\StudentInfoResolver;
use App\Book\StudentsResolver;
use App\Entity\Attendance;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\BookEvent;
use App\Entity\BookIntegrityCheckViolation;
use App\Entity\DateLesson;
use App\Entity\ExcuseNote;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\LessonEntry;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudentInformationType;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Grouping\DateWeekOfYearStrategy;
use App\Grouping\Grouper;
use App\Grouping\LessonDayStrategy;
use App\Grouping\TuitionGradeGroup;
use App\Grouping\TuitionGradeStrategy;
use App\Messenger\RunIntegrityCheckMessage;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\BookIntegrityCheckViolationRepositoryInterface;
use App\Repository\StudentInformationRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\GradeResponsibilityRepositoryInterface;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\BookIntegrityCheckViolationVoter;
use App\Security\Voter\LessonEntryVoter;
use App\Settings\BookSettings;
use App\Settings\TimetableSettings;
use App\Settings\TuitionGradebookSettings;
use App\Sorting\BookCommentDateStrategy;
use App\Sorting\DateStrategy;
use App\Sorting\DateWeekOfYearGroupStrategy;
use App\Sorting\LessonDayGroupStrategy;
use App\Sorting\LessonStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Sorting\StringGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentAwareGradeFilter;
use App\View\Filter\StudentAwareTuitionFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\TuitionFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/book')]
#[IsFeatureEnabled(Feature::Book)]
class BookController extends AbstractController {

    use CalendarWeeksTrait;

    private const ItemsPerPage = 25;

    private const StudentsPerPage = 35;

    private const ToggleSuppressCsrfId = 'book.integrity_check.suppress';

    public function __construct(RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    private function getClosestMonthStart(DateTime $dateTime): DateTime {
        $dateTime = clone $dateTime;
        $dateTime->setDate((int)$dateTime->format('Y'), (int)$dateTime->format('m'), 1);
        return $dateTime;
    }

    /**
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
        } catch (Exception) {
            $selectedDate = null;
        }

        if($selectedDate === null && $currentSection !== null) {
            $selectedDate = $this->getClosestMonthStart($dateHelper->getToday());
        }

        if($selectedDate !== null && $currentSection !== null && $dateHelper->isBetween($selectedDate, $currentSection->getStart(), $currentSection->getEnd()) !== true) {
            // Additional check if maybe parts of the month are inside the selected section (at the beginning)
            $start = clone $selectedDate;
            $end = (clone $selectedDate)->modify('+1 month')->modify('-1 day');

            // case 1: selected month is partially at the beginning of the section
            if($dateHelper->isBetween($start, $currentSection->getStart(), $currentSection->getEnd()) === false && $dateHelper->isBetween($end, $currentSection->getStart(), $currentSection->getEnd())) {
                $selectedDate = clone $currentSection->getStart();
            } else {
                $selectedDate = $this->getClosestMonthStart($currentSection->getEnd());
            }
        }

        return $selectedDate;
    }

    /**
     * @return Tuition[]
     */
    private function resolveOwnTuitions(?Section $currentSection, User $user, TuitionRepositoryInterface $tuitionRepository): array {
        if($currentSection === null) {
            return [ ];
        }

        if ($user->isStudentOrParent()) {
            return $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $currentSection);
        } else if ($user->isTeacher()) {
            return $tuitionRepository->findAllByTeacher($user->getTeacher(), $currentSection);
        }

        return [ ];
    }

    /**
     * @return Grade[]
     */
    private function resolveOwnGrades(?Section $currentSection, User $user): array {
        if($currentSection === null) {
            return [ ];
        }

        if ($user->isStudentOrParent()) {
            return ArrayUtils::unique(
                $user->getStudents()->map(fn(Student $student) => $student->getGrade($currentSection))
            );
        } else if ($user->isTeacher()) {
            return $user->getTeacher()->getGrades()->
                filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection() === $currentSection)
                ->map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getGrade())
                ->toArray();
        }

        return [ ];
    }

    #[Route(path: '/entry', name: 'book')]
    public function index(SectionFilter                          $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter, TeacherFilter $teacherFilter,
                          TuitionRepositoryInterface             $tuitionRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository, DateHelper $dateHelper, Request $request,
                          EntryOverviewHelper                    $entryOverviewHelper, AbsenceExcuseResolver $absenceExcuseResolver, BookSettings $settings,
                          GradeResponsibilityRepositoryInterface $responsibilityRepository, LessonEntryRepositoryInterface $lessonEntryRepository, StudentInformationRepositoryInterface $studentInformationRepository): Response {
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
        $responsibilities = [ ];
        $entriesWithExercises = [ ];
        $studentExtraInfo = [ ];

        if($selectedDate !== null) {
            if ($gradeFilterView->getCurrentGrade() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForGrade($gradeFilterView->getCurrentGrade(), $selectedDate, (clone $selectedDate)->modify('+6 days'));

                $students = $gradeFilterView->getCurrentGrade()->getMemberships()->filter(fn(GradeMembership $membership) => $membership->getSection()->getId() === $sectionFilterView->getCurrentSection()->getId())->map(fn(GradeMembership $membership) => $membership->getStudent())->toArray();
                $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()], $sectionFilterView->getCurrentSection(), true);
                $info = $absenceExcuseResolver->resolveBulk($students, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd(), true, $tuitions);

                if($sectionFilterView->getCurrentSection() !== null) {
                    $responsibilities = $responsibilityRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection());
                }

                $studentExtraInfo = $studentInformationRepository->findByGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection(), StudentInformationType::Lessons, $selectedDate, (clone $selectedDate)->modify('+6 days'));
                $entriesWithExercises = $lessonEntryRepository->findAllByGradeWithExercises($gradeFilterView->getCurrentGrade(), $dateHelper->getToday()->modify(sprintf('-%d days', $settings->getExercisesDays())), $dateHelper->getToday());
            } else if ($tuitionFilterView->getCurrentTuition() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition(), $selectedDate, (clone $selectedDate)->modify('+1 month')->modify('-1 day'));

                $students = $tuitionFilterView->getCurrentTuition()->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent());
                $info = $absenceExcuseResolver->resolveBulk($students->toArray(), $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd(), false, [ $tuitionFilterView->getCurrentTuition() ]);

                if($sectionFilterView->getCurrentSection() !== null && $tuitionFilterView->getCurrentTuition()->getStudyGroup()->getGrades()->count() === 1) {
                    $responsibilities = $responsibilityRepository->findAllByGrade($tuitionFilterView->getCurrentTuition()->getStudyGroup()->getGrades()->first(), $sectionFilterView->getCurrentSection());
                }

                $studentExtraInfo = $studentInformationRepository->findByStudyGroup($tuitionFilterView->getCurrentTuition()->getStudyGroup(), StudentInformationType::Lessons, $selectedDate, (clone $selectedDate)->modify('+6 days'));
            } else if($teacherFilterView->getCurrentTeacher() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate, (clone $selectedDate)->modify('+6 days'));
                $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());

                // IDs of already handled students
                $studentIds = [ ];
                $students = [ ];

                foreach($tuitions as $tuition) {
                    /** @var StudyGroupMembership $membership */
                    foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                        $student = $membership->getStudent();

                        if(in_array($student->getId(), $studentIds)) {
                            continue;
                        }

                        $studentIds[] = $student->getId();
                        $students[] = $student;
                    }
                }

                $info = $absenceExcuseResolver->resolveBulk($students, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd(), false, $tuitions);
                $studentExtraInfo = $studentInformationRepository->findByStudents($students, StudentInformationType::Lessons, $selectedDate, (clone $selectedDate)->modify('+6 days'));
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
            array_map(fn(StudentInfo $info) => $info->getNotExcusedOrNotSetLessonsCount(), $missingExcuses));

        $weekStarts = [ ];
        $monthStarts = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
            $monthStarts = $this->listCalendarMonths($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        $lateStudentsByLesson = [ ];
        $absentStudentsByLesson = [ ];
        $lateStudentsByEvent = [ ];
        $absentStudentsByEvent = [ ];

        if($overview !== null) {
            $excusesByStudent = [];
            foreach($overview->getEvents() as $event) {
                $uuid = $event->getUuid()->toString();
                $lateStudentsByEvent[$uuid] = [ ];
                $absentStudentsByEvent[$uuid] = [ ];

                foreach($event->getAttendances() as $attendance) {
                    if($gradeFilterView->getCurrentGrade() !== null && $attendance->getStudent()->getGrade($sectionFilterView->getCurrentSection())?->getId() !== $gradeFilterView->getCurrentGrade()->getId()) {
                        continue;
                    }

                    if($attendance->getType() === AttendanceType::Late) {
                        $lateStudentsByEvent[$uuid][] = $attendance;
                    } else if($attendance->getType() === AttendanceType::Absent) {
                        $studentUuid = $attendance->getStudent()->getUuid()->toString();

                        if (!isset($excusesByStudent[$studentUuid])) {
                            $excusesByStudent[$studentUuid] = $excuseNoteRepository->findByStudentsAndDate([$attendance->getStudent()], $event->getDate());
                        }

                        /** @var ExcuseNote $excuseNote */
                        foreach ($excusesByStudent[$studentUuid] as $excuseNote) {
                            if ((new DateLesson())->setDate($event->getDate())->setLesson($attendance->getLesson())->isBetween($excuseNote->getFrom(), $excuseNote->getUntil())) {
                                $attendance->setExcuseStatus(AttendanceExcuseStatus::Excused);
                            }
                        }

                        $absentStudentsByEvent[$uuid][] = $attendance;
                    }
                }
            }

            foreach ($overview->getDays() as $day) {
                $excusesByStudent = [];

                foreach ($day->getLessons() as $lesson) {
                    if ($lesson->getEntry() === null) {
                        continue;
                    }

                    $uuid = $lesson->getEntry()->getUuid()->toString();
                    $lateStudentsByLesson[$uuid] = [];
                    $absentStudentsByLesson[$uuid] = [];

                    if ($lesson->getAbsentCount() === 0 && $lesson->getLateCount() === 0) {
                        continue;
                    }

                    /** @var Attendance $attendance */
                    foreach ($lesson->getEntry()->getAttendances() as $attendance) {
                        if($gradeFilterView->getCurrentGrade() !== null && $attendance->getStudent()->getGrade($sectionFilterView->getCurrentSection())?->getId() !== $gradeFilterView->getCurrentGrade()->getId()) {
                            continue;
                        }

                        if ($attendance->getType() === AttendanceType::Late) {
                            $lateStudentsByLesson[$uuid][] = $attendance;
                        } else {
                            if ($attendance->getType() === AttendanceType::Absent) {
                                $studentUuid = $attendance->getStudent()->getUuid()->toString();

                                if (!isset($excusesByStudent[$studentUuid])) {
                                    $excusesByStudent[$studentUuid] = $excuseNoteRepository->findByStudentsAndDate([$attendance->getStudent()], $day->getDate());
                                }

                                /** @var ExcuseNote $excuseNote */
                                foreach ($excusesByStudent[$studentUuid] as $excuseNote) {
                                    if ((new DateLesson())->setDate($day->getDate())->setLesson($lesson->getLessonNumber())->isBetween($excuseNote->getFrom(), $excuseNote->getUntil())) {
                                        $attendance->setExcuseStatus(AttendanceExcuseStatus::Excused);
                                    }
                                }

                                $absentStudentsByLesson[$uuid][] = $attendance;
                            }
                        }
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
            'lateStudentsByLesson' => $lateStudentsByLesson,
            'absentStudentsByEvent' => $absentStudentsByEvent,
            'lateStudentsByEvent' => $lateStudentsByEvent,
            'responsibilities' => $responsibilities,
            'entriesWithExercises' => $entriesWithExercises,
            'exercisesDays' => $settings->getExercisesDays(),
            'studentExtraInfo' => $studentExtraInfo
        ]);
    }

    #[Route(path: '/missing', name: 'missing_book_entries')]
    public function missing(Request $request, SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter,
                            TuitionFilter $tuitionFilter, TimetableLessonRepositoryInterface $lessonRepository, TuitionRepositoryInterface $tuitionRepository,
                            DateHelper $dateHelper, Sorter $sorter, Grouper $grouper): Response {
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
        $sorter->sort($groups, LessonDayGroupStrategy::class, SortDirection::Descending);
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

    #[Route(path: '/student', name: 'book_students')]
    public function students(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter, TeacherFilter $teacherFilter,
                             TuitionRepositoryInterface $tuitionRepository, StudentRepositoryInterface $studentRepository, LessonAttendanceFlagRepositoryInterface $flagRepository, StudentInfoResolver $studentInfoResolver,
                             Sorter $sorter, StudentsResolver $studentsResolver, Request $request, StudentInfoCountsGenerator $studentInfoCountsGenerator, CacheWarmupHelper $cacheWarmupHelper): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $page = $request->query->getInt('page', 1);
        $paginator = [ ];
        $tuitions = [ ];
        $context = null;
        if($gradeFilterView->getCurrentGrade() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $context = $gradeFilterView->getCurrentGrade();
            $paginator = $studentRepository->getStudentsByGradePaginator(self::StudentsPerPage, $page, $gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection());
        } else if($tuitionFilterView->getCurrentTuition() !== null) {
            $context = $tuitionFilterView->getCurrentTuition();
            $paginator = $studentsResolver->resolvePaginated(self::StudentsPerPage, $page, $tuitionFilterView->getCurrentTuition(), false, true);
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $context = $teacherFilterView->getCurrentTeacher();
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
            $studyGroups = array_map(fn(Tuition $tuition) => $tuition->getStudyGroup(), $tuitions);

            $paginator = $studentRepository->getStudentsByStudyGroupsPaginator(self::StudentsPerPage, $page, $studyGroups);
        }

        if($sectionFilterView->getCurrentSection() !== null && $request->isMethod(Request::METHOD_POST) && $this->isCsrfTokenValid('regenerate', $request->request->get('_csrf_token'))) {
            if($gradeFilterView->getCurrentGrade() !== null) {
                $cacheWarmupHelper->warmupGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection());
            } else if($tuitionFilterView->getCurrentTuition() !== null) {
                $cacheWarmupHelper->warmupTuition($tuitionFilterView->getCurrentTuition(), $sectionFilterView->getCurrentSection());
            } else if($teacherFilterView->getCurrentTeacher() !== null) {
                $cacheWarmupHelper->warmupTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
            }

            $this->addFlash('success', 'book.students.cache.regenerate.success');
            return $this->redirectToRoute('book_students', [
                'section' => $sectionFilterView->getCurrentSection()->getUuid()->toString(),
                'tuition' => $tuitionFilterView->getCurrentTuition()?->getUuid()->toString(),
                'grade' => $gradeFilterView->getCurrentGrade()?->getUuid()->toString(),
                'teacher' => $teacherFilterView->getCurrentTeacher()?->getUuid()->toString(),
                'page' => $page
            ]);
        }

        $students = !is_array($paginator) ? iterator_to_array($paginator->getIterator()) : $paginator;
        $pages = ceil((float)count($paginator) / self::StudentsPerPage);

        $sorter->sort($students, StudentStrategy::class);
        $info = [ ];

        foreach($students as $student) {
            $info[$student->getId()] = $studentInfoCountsGenerator->generate($student, $sectionFilterView->getCurrentSection(), $context);
        }

        return $this->render('books/students.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'info' => $info,
            'flags' => $flagRepository->findAll(),
            'page' => $page,
            'pages' => $pages,
            'students' => $students
        ]);
    }

    #[Route(path: '/student/{student}/attendance', name: 'book_student_attendance')]
    public function studentAttendance(#[MapEntity(mapping: ['student' => 'uuid'])] Student $student, SectionFilter $sectionFilter, StudentAwareTuitionFilter $tuitionFilter,
                            StudentAwareGradeFilter $gradeFilter, TeacherFilter  $teacherFilter, Request $request,
                            StudentInfoResolver $infoResolver, TuitionRepositoryInterface $tuitionRepository,
                            Sorter $sorter, Grouper $grouper, DateHelper $dateHelper, TimetableSettings $timetableSettings,
                            UrlGeneratorInterface $urlGenerator,
                            LessonEntryRepositoryInterface $entryRepository, LessonAttendanceRepositoryInterface $lessonAttendanceRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $student);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $student);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $tuitions = [ ];
        $includeEvents = false;

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $tuitions[] = $tuitionFilterView->getCurrentTuition();
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $tuitions = [];
            $includeEvents = true;
        }

        // Filter tuitions which the student is a part of
        $tuitions = array_filter($tuitions, function(Tuition $tuition) use ($student) {
            foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
                if($membership->getStudent()->getId() === $student->getId()) {
                    return true;
                }
            }
            return false;
        });

        $min = $sectionFilterView->getCurrentSection()->getStart();
        $max = min(
            $dateHelper->getToday(),
            $sectionFilterView->getCurrentSection()->getEnd()
        );

        $entries = [ ];
        $events = [ ];
        foreach($tuitions as $tuition) {
            $entries = array_merge($entries, $entryRepository->findAllByTuition($tuition, $min, $max));
        }

        foreach($lessonAttendanceRepository->findByStudentAndDateRange($student, $min, $max, true) as $attendance) {
            if($attendance->getEntry() !== null && !in_array($attendance->getEntry(), $entries)) {
                $entries[] = $attendance->getEntry();
            } else if($attendance->getEvent() !== null && !in_array($attendance->getEvent(), $events)) {
                $events[] = $attendance->getEvent();
            }
        }

        /**
         * @var int $key
         * @var LessonEntry $entry
         */
        foreach($entries as $key => $entry) {
            $lesson = null;

            if($entry->getLesson() !== null) {
                $lesson = [
                    'uuid' => $entry->getLesson()->getUuid()->toString(),
                    'date' => $entry->getLesson()->getDate()->format('c'),
                    'start' => $entry->getLesson()->getLessonStart(),
                    'end' => $entry->getLesson()->getLessonEnd(),
                    'teachers' => $entry->getLesson()->getTeachers()->map(fn(Teacher $teacher) => $teacher->getAcronym())->toArray(),
                    'subject' => $entry->getLesson()->getSubjectName()
                ];
            }

            $entries[$key] = [
                'uuid' => $entry->getUuid()->toString(),
                'lesson' => $lesson,
                'start' => $entry->getLessonStart(),
                'end' => $entry->getLessonEnd(),
                'is_cancelled' => $entry->isCancelled(),
                'url' => $urlGenerator->generate('show_entry', ['uuid' => $entry->getUuid()->toString()])
            ];

            if($entry->isCancelled()) {
                $entries[$key]['cancel_reason'] = $entry->getCancelReason();
            }
        }

        /**
         * @var int $key
         * @var BookEvent $event
         */
        foreach($events as $key => $event) {
            $events[$key] = [
                'uuid' => $event->getUuid()->toString(),
                'date' => $event->getDate()->format('c'),
                'start' => $event->getLessonStart(),
                'end' => $event->getLessonEnd(),
                'teacher' => $event->getTeacher()->getAcronym(),
                'title' => $event->getTitle(),
                'url' => $urlGenerator->generate('show_or_edit_book_event_entry', ['uuid' => $event->getUuid()->toString()])
            ];
        }

        if($includeEvents === false) {
            $events = [ ];
        }

        $info = $infoResolver->resolveStudentInfo($student, $sectionFilterView->getCurrentSection(), $tuitions, includeEvents: $includeEvents);

        $days = $this->getListOfDays($min, $max, $timetableSettings->getDays());
        $groups = $grouper->group($days, DateWeekOfYearStrategy::class);

        $sorter->sort($groups, DateWeekOfYearGroupStrategy::class, SortDirection::Descending);
        $sorter->sortGroupItems($groups, DateStrategy::class, SortDirection::Descending);

        return $this->render('books/student_attendance.html.twig', [
            'student' => $student,
            'info' => $info,
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'teacherFilter' => $teacherFilterView,
            'numberOfLessons' => $timetableSettings->getMaxLessons(),
            'entries' => array_values($entries),
            'events' => array_values($events)
        ]);
    }

    #[Route(path: '/student/{student}/comments', name: 'book_student_comments')]
    public function studentComments(#[MapEntity(mapping: ['student' => 'uuid'])] Student $student, SectionFilter $sectionFilter, Request $request, Sorter $sorter,
                                    BookCommentRepositoryInterface $commentRepository): Response {

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $comments = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $comments = $commentRepository->findAllByDateAndStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        $sorter->sort($comments, BookCommentDateStrategy::class, SortDirection::Descending);

        return $this->render('books/student_comments.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'student' => $student,
            'comments' => $comments
        ]);
    }

    /**
     * @param DateTime $min
     * @param DateTime $max
     * @param int[] $days
     * @return DateTime[]
     */
    private function getListOfDays(DateTime $min, DateTime $max, array $days): array {
        $result = [ ];
        $current = clone $min;
        while($current <= $max) {
            if(in_array((int)$current->format('w'), $days)) {
                $result[] = clone $current;
            }
            $current = $current->modify('+1 day');
        }

        return $result;
    }

    private function createResponse(string $content, string $contentType, string $filename): Response {
        $response = new Response($content);
        $response->headers->set('Content-Type', $contentType . '; charset=UTF-8');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, transliterator_transliterate('Latin-ASCII', $filename)));

        return $response;
    }

    #[Route(path: '/export', name: 'book_export')]
    public function export(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter,
                           TuitionRepositoryInterface $tuitionRepository, BookLessonCountGenerator $bookLessonCountGenerator,
                           Request $request, Grouper $grouper, Sorter $sorter,  TuitionGradebookSettings $gradebookSettings) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null);

        if($gradeFilterView->getCurrentGrade() !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()], $sectionFilterView->getCurrentSection());
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
        } else {
            $tuitions = [ ];
        }

        $holtCounts = [ ];
        $missingCounts = [ ];

        foreach($tuitions as $tuition) {
            $stats = $bookLessonCountGenerator->getCount($tuition);
            $holtCounts[$tuition->getId()] = $stats->holdLessonsCount;
            $missingCounts[$tuition->getId()] = $stats->missingLessonsCount;
        }

        $groups = $grouper->group($tuitions, TuitionGradeStrategy::class);
        $sorter->sort($groups, StringGroupStrategy::class);
        $sorter->sortGroupItems($groups, TuitionStrategy::class);

        if($gradeFilterView->getCurrentGrade() !== null) {
            $groups = array_filter($groups, fn(TuitionGradeGroup $group) => $group->getGrade()->getId() === $gradeFilterView->getCurrentGrade()->getId());
        }

        return $this->render('books/export.html.twig', [
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'holdCounts' => $holtCounts,
            'missingCounts' => $missingCounts,
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage()
        ]);
    }

    private function computeFileName(Tuition $tuition, Section $section, string $extension): string {
        $grades = $tuition->getStudyGroup()->getGrades()->map(fn(Grade $grade) => $grade->getName())->toArray();
        usort($grades, 'strnatcasecmp');

        return sprintf(
            '%d-%d-%s-%s.%s',
            $section->getYear(),
            $section->getNumber(),
            implode('-', $grades),
            $tuition->getName(),
            $extension
        );
    }

    #[Route(path: '/{section}/t/{tuition}/export/json', name: 'book_export_tuition_json')]
    public function exportTutionJson(#[MapEntity(mapping: ['tuition' => 'uuid'])] Tuition $tuition, #[MapEntity(mapping: ['section' => 'uuid'])] Section $section, BookExporter $exporter): Response {
        $filename = $this->computeFileName($tuition, $section, 'json');
        $json = $exporter->exportTuitionJson($tuition, $section);
        return $this->createResponse($json, 'application/json', $filename);
    }

    #[Route(path: '/{section}/t/{tuition}/export/xml', name: 'book_export_tuition_xml')]
    public function exportTuitionXml(#[MapEntity(mapping: ['tuition' => 'uuid'])] Tuition $tuition, #[MapEntity(mapping: ['section' => 'uuid'])] Section $section, BookExporter $exporter): Response {
        $filename = $this->computeFileName($tuition, $section, 'xml');
        $xml = $exporter->exportTuitionXml($tuition, $section);
        return $this->createResponse($xml, 'application/xml', $filename);
    }

    #[Route(path: '/{section}/g/{grade}/export/json', name: 'book_export_grade_json')]
    public function exportGradeJson(#[MapEntity(mapping: ['grade' => 'uuid'])] Grade $grade, #[MapEntity(mapping: ['section' => 'uuid'])] Section $section, BookExporter $exporter): Response {
        $filename = sprintf('%s-%d-%d.json', $grade->getName(), $section->getYear(), $section->getNumber());
        $json = $exporter->exportGradeJson($grade, $section);
        return $this->createResponse($json, 'application/json', $filename);
    }

    #[Route(path: '/{section}/g/{grade}/export/xml', name: 'book_export_grade_xml')]
    public function exportGradeXml(#[MapEntity(mapping: ['grade' => 'uuid'])] Grade $grade, #[MapEntity(mapping: ['section' => 'uuid'])] Section $section, BookExporter $exporter): Response {
        $filename = sprintf('%s-%d-%d.xml', $grade->getName(), $section->getYear(), $section->getNumber());
        $xml = $exporter->exportGradeXml($grade, $section);
        return $this->createResponse($xml, 'application/xml', $filename);
    }

    #[Route('/check/{uuid}/toggleSuppress', methods: ['POST'])]
    public function toggleSuppressViolation(BookIntegrityCheckViolation $violation, BookIntegrityCheckViolationRepositoryInterface $violationRepository, Request $request): Response {
        $this->denyAccessUnlessGranted(BookIntegrityCheckViolationVoter::Suppress, $violation);

        if($this->isCsrfTokenValid(self::ToggleSuppressCsrfId, $request->request->get('_token')) === true) {
            $violation->setIsSuppressed(!$violation->isSuppressed());
            $violationRepository->persist($violation);
        }

        return new JsonResponse([
            'is_suppressed' => $violation->isSuppressed()
        ]);
    }

    #[Route('/check', name: 'book_integrity_check')]
    public function integrityCheck(StudyGroupFilter $studyGroupFilter, TeacherFilter $teacherFilter, SectionFilter $sectionFilter,
                                   TuitionRepositoryInterface $tuitionRepository, StudentRepositoryInterface $studentRepository,
                                   MessageBusInterface $messageBus, ViolationsResolver $violationsResolver,
                                   Request $request, IntegrityCheckRunner $runner, Sorter $sorter): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, !$request->query->has('study_group'));

        $results = [ ];
        $students = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            /** @var Student[] $students */
            $students = $studyGroupFilterView->getCurrentStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
            $studyGroups = array_map(fn(Tuition $tuition) => $tuition->getStudyGroup(), $tuitions);
            $students = $studentRepository->findAllByStudyGroups($studyGroups);
        }

        $sorter->sort($students, StudentStrategy::class);

        if($request->query->get('run') === '✓' && count($students) > 0) {
            // handle async
            foreach($students as $student) {
                $messageBus->dispatch(new RunIntegrityCheckMessage($student->getId(), $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd()));
            }

            $this->addFlash('success', 'book.integrity_check.run.success.async');

            return $this->redirectToRoute('book_integrity_check', [
                'study_group' => $studyGroupFilterView->getCurrentStudyGroup()?->getUuid(),
                'teacher' => $teacherFilterView->getCurrentTeacher()?->getUuid()
            ]);
        }

        foreach($students as $student) {
            $results[] = $violationsResolver->resolve($student, $sectionFilterView->getCurrentSection(), $teacherFilterView->getCurrentTeacher());
        }

        return $this->render('books/integrity_check.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'teacherFilter' => $teacherFilterView,
            'results' => $results,
            'enabledChecks' => $runner->getEnabledChecks(),
            'csrfTokenId' => self::ToggleSuppressCsrfId
        ]);
    }
}
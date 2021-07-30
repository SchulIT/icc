<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Book\Export\BookExporter;
use App\Book\Lesson;
use App\Book\Student\AbsenceExcuseResolver;
use App\Book\Student\StudentInfo;
use App\Book\Student\StudentInfoResolver;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\Lesson as LessonEntity;
use App\Grouping\GenericDateStrategy;
use App\Grouping\Grouper;
use App\Grouping\LessonAttendanceCommentsGroup;
use App\Grouping\LessonAttendanceDateStrategy;
use App\Grouping\LessonDayStrategy;
use App\Repository\LessonRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
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

    private const ItemsPerPage = 25;

    private function getClosestWeekStart(DateTime $dateTime): DateTime {
        $dateTime = clone $dateTime;

        while((int)$dateTime->format('N') > 1) {
            $dateTime = $dateTime->modify('-1 day');
        }

        return $dateTime;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return DateTime[] All mondays with their week numbers as key
     */
    private function listCalendarWeeks(DateTime $start, DateTime $end): array {
        $weekStarts = [ ];
        $current = $this->getClosestWeekStart($start);

        while($current < $end) {
            $weekStarts[(int)$current->format('W')] = clone $current;
            $current = $current->modify('+7 days');
        }

        return $weekStarts;
    }

    private function resolveSelectedDate(Request $request, ?Section $currentSection, DateHelper $dateHelper): ?DateTime {
        $selectedDate = null;
        try {
            if($request->query->has('date')) {
                $selectedDate = new DateTime($request->query->get('date', null));
                $selectedDate->setTime(0, 0, 0);
            }
        } catch (Exception $e) {
            $selectedDate = null;
        }

        if($selectedDate === null && $currentSection !== null) {
            $selectedDate = $this->getClosestWeekStart($dateHelper->getToday());
        }

        if($selectedDate !== null && $currentSection !== null && $dateHelper->isBetween($selectedDate, $currentSection->getStart(), $currentSection->getEnd()) !== true) {
            $selectedDate = $this->getClosestWeekStart($currentSection->getEnd());
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
                          TuitionRepositoryInterface $tuitionRepository, DateHelper $dateHelper, Request $request,
                          EntryOverviewHelper $entryOverviewHelper, AbsenceExcuseResolver $absenceExcuseResolver) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $tuitionFilterView->getCurrentTuition() === null);

        $selectedDate = $this->resolveSelectedDate($request, $sectionFilterView->getCurrentSection(), $dateHelper);

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
                $overview = $entryOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition(), $selectedDate, (clone $selectedDate)->modify('+6 days'));

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

        $missingExcuses = array_filter($info, function(StudentInfo $info) {
            return $info->getNotExcusedOrNotSetLessonsCount() > 0;
        });
        $missingExcuseCount = array_sum(
            array_map(function(StudentInfo $info) {
                return $info->getNotExcusedOrNotSetLessonsCount();
            }, $missingExcuses));

        $weekStarts = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
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
            'missingExcuses' => $missingExcuses,
            'missingExcusesCount' => $missingExcuseCount
        ]);
    }

    /**
     * @Route("/missing", name="missing_book_entries")
     */
    public function missing(Request $request, SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter,
                            TuitionFilter $tuitionFilter, LessonRepositoryInterface $lessonRepository, TuitionRepositoryInterface $tuitionRepository,
                            DateHelper $dateHelper, Sorter $sorter, Grouper $grouper) {
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
                $paginator = $lessonRepository->getMissingByGradePaginator(static::ItemsPerPage, $page, $gradeFilterView->getCurrentGrade(), $start, $end);
            } elseif($tuitionFilterView->getCurrentTuition() !== null) {
                $paginator = $lessonRepository->getMissingByTuitionPaginator(static::ItemsPerPage, $page, $tuitionFilterView->getCurrentTuition(), $start, $end);
            } else if($teacherFilterView->getCurrentTeacher() !== null) {
                $paginator = $lessonRepository->getMissingByTeacherPaginator(static::ItemsPerPage, $page, $teacherFilterView->getCurrentTeacher(), $start, $end);
            }
        }

        $missing = [ ];
        $pages = 0;

        if($paginator !== null) {
            $missing = [ ];
            $pages = ceil((float)$paginator->count() / static::ItemsPerPage);

            /** @var LessonEntity $lessonEntity */
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
    public function students(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter,
                             TuitionRepositoryInterface $tuitionRepository, StudentRepositoryInterface $studentRepository, StudentInfoResolver $studentInfoResolver,
                             Sorter $sorter, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user, $tuitionFilterView->getCurrentTuition() === null);

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $students = [ ];
        if($gradeFilterView->getCurrentGrade() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $students = $studentRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection());
        } else if($tuitionFilterView->getCurrentTuition() !== null) {
            $students = $studentRepository->findAllByStudyGroups([$tuitionFilterView->getCurrentTuition()->getStudyGroup()]);
        }

        $sorter->sort($students, StudentStrategy::class);
        $info = [ ];

        foreach($students as $student) {
            $info[] = $studentInfoResolver->resolveStudentInfo($student, $sectionFilterView->getCurrentSection(), $tuitionFilterView->getCurrentTuition());
        }

        return $this->render('books/students.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'info' => $info
        ]);
    }

    /**
     * @Route("/student/{section}/{student}", name="book_student")
     * @ParamConverter("section", class="App\Entity\Section", options={"mapping": {"section": "uuid"}})
     * @ParamConverter("student", class="App\Entity\Student", options={"mapping": {"student": "uuid"}})
     */
    public function student(Student $student, Section $section, StudentInfoResolver $infoResolver, Sorter $sorter, Grouper $grouper) {
        $info = $infoResolver->resolveStudentInfo($student, $section);
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
            'section' => $section
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
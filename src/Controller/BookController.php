<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Book\Student\StudentInfoResolver;
use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Form\LessonEntryCancelType;
use App\Grouping\Grouper;
use App\Grouping\LessonAttendanceDateStrategy;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\LessonAttendanceGroupStrategy;
use App\Sorting\LessonAttendanceStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\TuitionFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController {

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

    private function resolveSelectedDate(Request $request, ?Section $currentSection, DateHelper $dateHelper): DateTime {
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
     * @Route("/overview", name="book")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter,
                          TuitionRepositoryInterface $tuitionRepository, DateHelper $dateHelper, Request $request, EntryOverviewHelper $entryOverviewHelper) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);

        $selectedDate = $this->resolveSelectedDate($request, $sectionFilterView->getCurrentSection(), $dateHelper);

        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        // Lessons / Entries
        $overview = null;

        if($selectedDate !== null) {
            if ($gradeFilterView->getCurrentGrade() !== null) {
                $overview = $entryOverviewHelper->computeOverviewForGrade($gradeFilterView->getCurrentGrade(), $selectedDate, (clone $selectedDate)->modify('+6 days'));
            } else {
                if ($tuitionFilterView->getCurrentTuition() !== null) {
                    $overview = $entryOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition(), $selectedDate, (clone $selectedDate)->modify('+6 days'));
                }
            }
        }

        $weekStarts = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        return $this->render('books/index.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'ownGrades' => $ownGrades,
            'ownTuitions' => $ownTuitions,
            'selectedDate' => $selectedDate,
            'overview' => $overview,
            'weekStarts' => $weekStarts
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
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);

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
                $info->getLateLessonAttendances()
            ), LessonAttendanceDateStrategy::class);

        $sorter->sort($groups, LessonAttendanceGroupStrategy::class, SortDirection::Descending());
        $sorter->sortGroupItems($groups, LessonAttendanceStrategy::class);

        return $this->render('books/student.html.twig', [
            'student' => $student,
            'info' => $info,
            'groups' => $groups,
            'section' => $section
        ]);
    }

}
<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\User;
use App\Entity\UserType;
use App\Form\LessonEntryCancelType;
use App\Repository\TuitionRepositoryInterface;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\TuitionFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
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

    /**
     * @Route("", name="book")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TuitionFilter $tuitionFilter,
                          TuitionRepositoryInterface $tuitionRepository, DateHelper $dateHelper, Request $request, EntryOverviewHelper $entryOverviewHelper) {
        /** @var User $user */
        $user = $this->getUser();

        $selectedDate = null;
        try {
            if($request->query->has('date')) {
                $selectedDate = new DateTime($request->query->get('date', null));
                $selectedDate->setTime(0, 0, 0);
            }
        } catch (Exception $e) {
            $selectedDate = null;
        }

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);

        if($selectedDate === null && $sectionFilterView->getCurrentSection() !== null) {
            $selectedDate = $this->getClosestWeekStart($dateHelper->getToday());
        }

        if($selectedDate !== null && $sectionFilterView->getCurrentSection() !== null && $dateHelper->isBetween($selectedDate, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd()) !== true) {
            $selectedDate = $this->getClosestWeekStart($sectionFilterView->getCurrentSection()->getEnd());
        }

        $ownGrades = [ ];
        $ownTuitions = [ ];

        // Compute own grades/tuition
        if($sectionFilterView->getCurrentSection() !== null) {
            if (EnumArrayUtils::inArray($user->getUserType(), [UserType::Student(), UserType::Parent()])) {
                $ownTuitions = $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $sectionFilterView->getCurrentSection());
                $ownGrades = $gradeFilterView->getGrades();
            } else if ($user->getUserType()->equals(UserType::Teacher())) {
                $ownTuitions = $tuitionRepository->findAllByTeacher($user->getTeacher(), $sectionFilterView->getCurrentSection());

                $ownGrades = $user->getTeacher()->getGrades()->
                        filter(function(GradeTeacher $gradeTeacher) use ($sectionFilterView) {
                            return $gradeTeacher->getSection() === $sectionFilterView->getCurrentSection();
                        })
                        ->map(function(GradeTeacher $gradeTeacher) {
                            return $gradeTeacher->getGrade();
                        })
                        ->toArray();
            }
        }

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
    public function students() {

    }

    /**
     * @Route("/student/{uuid}", name="book_student")
     */
    public function student() {

    }

}
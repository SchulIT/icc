<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\TimetablePeriod;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\MessageRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Sorting\TimetablePeriodStrategy;
use App\Timetable\TimetableHelper;
use App\View\Filter\GradeFilter;
use App\View\Filter\RoomFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\SubjectFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimetableController extends AbstractControllerWithMessages {

    private $timetableHelper;
    private $timetableSettings;
    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, TimetableHelper $timetableHelper, TimetableSettings $timetableSettings,
                                Grouper $grouper, Sorter $sorter) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper);

        $this->timetableHelper = $timetableHelper;
        $this->timetableSettings = $timetableSettings;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/timetable", name="timetable")
     */
    public function index(StudentFilter $studentFilter, TeacherFilter $teacherFilter, GradeFilter $gradeFilter, RoomFilter $roomFilter, SubjectFilter $subjectFilter,
                          TimetableWeekRepositoryInterface $weekRepository, TimetableLessonRepositoryInterface $lessonRepository, TimetablePeriodRepositoryInterface $periodRepository,
                          TimetableSupervisionRepositoryInterface $supervisionRepository, Request $request,
                          ?int $studentId = null, ?string $teacherAcronym = null, ?int $roomId = null, ?int $gradeId = null, ?bool $print = false) {
        $studentFilterView = $studentFilter->handle($studentId, $this->getUser());
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $this->getUser());
        $gradeFilterView = $gradeFilter->handle($gradeId, $this->getUser());
        $roomFilterView = $roomFilter->handle($roomId);
        $subjectFilterView = $subjectFilter->handle($request->query->get('subjects', [ ]));

        $periods = $periodRepository->findAll();
        $this->sorter->sort($periods, TimetablePeriodStrategy::class);

        $currentPeriod = $this->getCurrentPeriod($periods);

        $weeks = $weekRepository->findAll();

        $lessons = [ ];
        $supervisions = [ ];

        if($currentPeriod !== null) {
            if ($studentFilterView->getCurrentStudent() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndStudent($currentPeriod, $studentFilterView->getCurrentStudent());
            } else if ($teacherFilterView->getCurrentTeacher() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndTeacher($currentPeriod, $teacherFilterView->getCurrentTeacher());
                $supervisions = $supervisionRepository->findAllByPeriodAndTeacher($currentPeriod, $teacherFilterView->getCurrentTeacher());
            } else if ($gradeFilterView->getCurrentGrade() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndGrade($currentPeriod, $gradeFilterView->getCurrentGrade());
            } else if ($roomFilterView->getCurrentRoom() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndRoom($currentPeriod, $roomFilterView->getCurrentRoom());
            } else if (count($subjectFilterView->getSubjects()) > 0) {
                $lessons = $lessonRepository->findAllByPeriodAndSubjects($currentPeriod, $subjectFilterView->getCurrentSubjects());
            }
        }

        if(count($lessons) === 0 && count($supervisions) === 0) {
            $timetable = null;
        } else {
            $timetable = $this->timetableHelper->makeTimetable($weeks, $lessons, $supervisions);
        }

        $startTimes = [ ];
        $endTimes = [ ];

        for($lesson = 1; $lesson <= $this->timetableSettings->getMaxLessons(); $lesson++) {
            $startTimes[$lesson] = $this->timetableSettings->getStart($lesson);
            $endTimes[$lesson] = $this->timetableSettings->getEnd($lesson);
        }

        $template = 'timetable/index.html.twig';

        if($print === true) {
            $template = 'timetable/index_print.html.twig';
        }

        return $this->render($template, [
            'timetable' => $timetable,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'gradeFilter' => $gradeFilterView,
            'roomFilter'=> $roomFilterView,
            'subjectFilter' => $subjectFilterView,
            'periods' => $periods,
            'currentPeriod' => $currentPeriod,
            'startTimes' => $startTimes,
            'endTimes' => $endTimes
        ]);
    }

    /**
     * @Route("/timetable/print", name="print_timetable")
     */
    private function print() {

    }

    /**
     * @Route("/timetable/ics", name="timetable_ics")
     */
    public function ics() {

    }

    /**
     * @param TimetablePeriod[] $periods
     * @return TimetablePeriod|null
     */
    private function getCurrentPeriod(array $periods): ?TimetablePeriod {
        foreach($periods as $period) {
            if($this->dateHelper->isBetween($this->dateHelper->getToday(), $period->getStart(), $period->getEnd())) {
                return $period;
            }
        }

        return null;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Timetable();
    }
}
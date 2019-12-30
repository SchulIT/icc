<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\Message;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\ExamRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Timetable\TimetablePeriodHelper;
use App\Utils\EnumArrayUtils;
use App\Utils\StudyGroupHelper;

class DashboardViewHelper {

    private $substitutionRepository;
    private $examRepository;
    private $timetableRepository;
    private $supervisionRepository;
    private $messageRepository;

    private $studyGroupHelper;
    private $timetablePeriodHelper;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, ExamRepositoryInterface $examRepository,
                                TimetableLessonRepositoryInterface $timetableRepository, TimetableSupervisionRepositoryInterface $supervisionRepository,
                                MessageRepositoryInterface $messageRepository, StudyGroupHelper $studyGroupHelper, TimetablePeriodHelper $timetablePeriodHelper) {
        $this->substitutionRepository = $substitutionRepository;
        $this->examRepository = $examRepository;
        $this->timetableRepository = $timetableRepository;
        $this->supervisionRepository = $supervisionRepository;
        $this->messageRepository = $messageRepository;
        $this->studyGroupHelper = $studyGroupHelper;
        $this->timetablePeriodHelper = $timetablePeriodHelper;
    }

    public function createViewForTeacher(Teacher $teacher, \DateTime $dateTime): DashboardView {
        $view = new DashboardView();

        $currentPeriod = $this->getCurrentTimetablePeriod(UserType::Teacher(), $dateTime);

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $dateTime, $view, true);
            $this->addSupervisions($this->supervisionRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $view);
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), UserType::Teacher(), $dateTime), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForTeacher($teacher, $dateTime), $view);
        $this->addExams($this->examRepository->findAllByTeacher($teacher, $dateTime, true), $view);

        return $view;
    }

    public function createViewForStudentOrParent(Student $student, \DateTime $dateTime, UserType $userType): DashboardView {
        if(EnumArrayUtils::inArray($userType, [ UserType::Student(), UserType::Parent() ])) {
            throw new \InvalidArgumentException('$userType must be either Student or Parent, "%s" given.', $userType->getValue());
        }

        $view = new DashboardView();

        $studyGroups = $this->studyGroupHelper->getStudyGroups([$student])->toArray();

        $currentPeriod = $this->getCurrentTimetablePeriod($userType, $dateTime);

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndStudent($currentPeriod, $student), $dateTime, $view, false);
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $userType, $dateTime, $studyGroups), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForStudyGroups($studyGroups, $dateTime), $view);
        $this->addExams($this->examRepository->findAllByStudents([$student], $dateTime, true), $view);

        return $view;
    }

    public function createViewForUser(User $user, \DateTime $dateTime): DashboardView {
        $view = new DashboardView();

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $user->getUserType(), $dateTime), $view);

        return $view;
    }

    /**
     * @param iterable $lessons
     * @param \DateTime $dateTime
     * @param DashboardView $dashboardView
     * @param bool $computeAbsences
     */
    private function addTimetableLessons(iterable $lessons, \DateTime $dateTime, DashboardView $dashboardView, bool $computeAbsences): void {
        foreach($lessons as $lesson) {
            $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson(), $dateTime) : [ ];

            $dashboardView->addItem($lesson->getLesson(), new LessonViewItem($lesson, $absentStudents, false));

            if($lesson->isDoubleLesson()) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson() + 1, $dateTime) : [ ];
                $dashboardView->addItem($lesson->getLesson() + 1, new LessonViewItem($lesson, [ ], false));
            }
        }
    }

    /**
     * @param TimetableSupervision[] $supervisions
     * @param DashboardView $dashboardView
     */
    private function addSupervisions(iterable $supervisions, DashboardView $dashboardView): void {
        foreach($supervisions as $supervision) {
            if($supervision->isBefore()) {
                $dashboardView->addItemBefore($supervision->getLesson(), new SupervisionViewItem($supervision));
            } else {
                $dashboardView->addItem($supervision->getLesson(), new SupervisionViewItem($supervision));
            }
        }
    }

    /**
     * @param Substitution[] $substitutions
     * @param DashboardView $dashboardView
     */
    private function addSubstitutions(iterable $substitutions, DashboardView $dashboardView): void {
        foreach($substitutions as $substitution) {
            for($lesson = $substitution->getLessonStart(); $lesson <= $substitution->getLessonEnd(); $lesson++) {
                $dashboardView->addItem($lesson, new SubstitutionViewItem($substitution));
            }
        }
    }

    /**
     * @param Message[] $messages
     * @param DashboardView $dashboardView
     */
    private function addMessages(iterable $messages, DashboardView $dashboardView): void {
        foreach($messages as $message) {
            $dashboardView->addMessage($message);
        }
    }

    /**
     * @param Exam[] $exams
     * @param DashboardView $dashboardView
     */
    private function addExams(iterable $exams, DashboardView $dashboardView): void {
        foreach($exams as $exam) {
            for($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                $dashboardView->addItem($lesson, new ExamViewItem($exam));
            }
        }
    }

    private function getCurrentTimetablePeriod(UserType $userType, \DateTime $dateTime): ?TimetablePeriod {
        return $this->timetablePeriodHelper->getPeriod($userType, $dateTime);
    }

    private function computeAbsentStudents(TimetableLesson $lessonEntity, int $lesson, \DateTime $dateTime) {
        // TODO: Also add absent students because a study group is absent (this information is not provided yet)
        return $this->computeExamStudents($lessonEntity, $lesson, $dateTime);
    }

    private function computeExamStudents(TimetableLesson $lessonEntity, int $lesson, \DateTime $dateTime) {
        $lessonStudents = $lessonEntity
            ->getTuition()
            ->getStudyGroup()
            ->getMemberships()
            ->map(function (StudyGroupMembership $membership) {
                return $membership->getStudent();
            });

        $exams = $this->examRepository->findAllByDateAndLesson($dateTime, $lesson);
        $absentStudents = [ ];

        foreach($exams as $exam) {
            foreach ($exam->getStudents() as $examStudent) {
                foreach ($lessonStudents as $lessonStudent) {
                    if ($examStudent->getId() === $lessonStudent->getId()) {
                        $absentStudents[] = new AbsentExamStudent($lessonStudent, $exam);
                    }
                }
            }
        }

        return $absentStudents;
    }

}
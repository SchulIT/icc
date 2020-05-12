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
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\MessageVoter;
use App\Security\Voter\SubstitutionVoter;
use App\Sorting\AbsentStudentStrategy;
use App\Sorting\MessageStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetablePeriodHelper;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use App\Utils\StudyGroupHelper;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DashboardViewHelper {

    private $substitutionRepository;
    private $examRepository;
    private $timetableRepository;
    private $timetableWeekRepository;
    private $supervisionRepository;
    private $messageRepository;
    private $infotextRepository;
    private $absenceRepository;

    private $studyGroupHelper;
    private $timetablePeriodHelper;
    private $sorter;

    private $authorizationChecker;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, ExamRepositoryInterface $examRepository,
                                TimetableLessonRepositoryInterface $timetableRepository, TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableWeekRepositoryInterface $timetableWeekRepository,
                                MessageRepositoryInterface $messageRepository, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                                StudyGroupHelper $studyGroupHelper, TimetablePeriodHelper $timetablePeriodHelper, Sorter $sorter, AuthorizationCheckerInterface $authorizationChecker) {
        $this->substitutionRepository = $substitutionRepository;
        $this->examRepository = $examRepository;
        $this->timetableRepository = $timetableRepository;
        $this->timetableWeekRepository = $timetableWeekRepository;
        $this->supervisionRepository = $supervisionRepository;
        $this->messageRepository = $messageRepository;
        $this->infotextRepository = $infotextRepository;
        $this->absenceRepository = $absenceRepository;
        $this->studyGroupHelper = $studyGroupHelper;
        $this->timetablePeriodHelper = $timetablePeriodHelper;
        $this->sorter = $sorter;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createViewForTeacher(Teacher $teacher, \DateTime $dateTime): DashboardView {
        $view = new DashboardView();

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $dateTime, $view, true, $numberOfWeeks);
            $this->addSupervisions($this->supervisionRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $view);
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), UserType::Teacher(), $dateTime), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForTeacher($teacher, $dateTime), $view);
        $this->addExams($this->examRepository->findAllByTeacher($teacher, $dateTime, true), $view);
        $this->addInfotexts($dateTime, $view);

        return $view;
    }

    public function createViewForStudentOrParent(Student $student, \DateTime $dateTime, UserType $userType): DashboardView {
        if(!EnumArrayUtils::inArray($userType, [ UserType::Student(), UserType::Parent() ])) {
            throw new \InvalidArgumentException(sprintf('$userType must be either Student or Parent, "%s" given.', $userType->getValue()));
        }

        $view = new DashboardView();

        $studyGroups = $this->studyGroupHelper->getStudyGroups([$student])->toArray();

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndStudent($currentPeriod, $student), $dateTime, $view, false, $numberOfWeeks);
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $userType, $dateTime, $studyGroups), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForStudyGroups($studyGroups, $dateTime), $view);
        $this->addExams($this->examRepository->findAllByStudents([$student], $dateTime, true), $view);
        $this->addInfotexts($dateTime, $view);

        return $view;
    }

    public function createViewForUser(User $user, \DateTime $dateTime): DashboardView {
        $view = new DashboardView();

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $user->getUserType(), $dateTime), $view);

        return $view;
    }

    /**
     * @param TimetableLesson[] $lessons
     * @param \DateTime $dateTime
     * @param DashboardView $dashboardView
     * @param bool $computeAbsences
     */
    private function addTimetableLessons(iterable $lessons, \DateTime $dateTime, DashboardView $dashboardView, bool $computeAbsences, int $numberOfWeeks): void {
        foreach($lessons as $lesson) {
            $isWeek = (int)$dateTime->format('W') % $numberOfWeeks === $lesson->getWeek()->getWeekMod();
            $isDay = (int)$dateTime->format('N') === $lesson->getDay();

            if($isWeek === false || $isDay === false) {
                continue;
            }

            $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson(), $dateTime) : [ ];
            $dashboardView->addItem($lesson->getLesson(), new LessonViewItem($lesson, $absentStudents, false));

            if($lesson->isDoubleLesson()) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson() + 1, $dateTime) : [ ];
                $dashboardView->addItem($lesson->getLesson() + 1, new LessonViewItem($lesson, $absentStudents, false));
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
            if($this->authorizationChecker->isGranted(SubstitutionVoter::View, $substitution) !== true) {
                continue;
            }

            if($substitution->startsBefore()) {
                $dashboardView->addItemBefore($substitution->getLessonStart(), new SubstitutionViewItem($substitution));

                if($substitution->getLessonEnd() - $substitution->getLessonStart() === 0) {
                    // Do not expand more lessons when the end is the same lesson as the beginning
                    continue;
                }
            }

            for ($lesson = $substitution->getLessonStart(); $lesson <= $substitution->getLessonEnd(); $lesson++) {
                $dashboardView->addItem($lesson, new SubstitutionViewItem($substitution));
            }
        }
    }

    /**
     * @param Message[] $messages
     * @param DashboardView $dashboardView
     */
    private function addMessages(iterable $messages, DashboardView $dashboardView): void {
        $this->sorter->sort($messages, MessageStrategy::class);

        foreach($messages as $message) {
            if($this->authorizationChecker->isGranted(MessageVoter::View, $message) !== true) {
                continue;
            }

            $dashboardView->addMessage($message);
        }
    }

    /**
     * @param Exam[] $exams
     * @param DashboardView $dashboardView
     */
    private function addExams(iterable $exams, DashboardView $dashboardView): void {
        foreach($exams as $exam) {
            if($this->authorizationChecker->isGranted(ExamVoter::Show, $exam) !== true) {
                continue;
            }

            for($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                $dashboardView->addItem($lesson, new ExamViewItem($exam));
            }
        }
    }

    /**
     * @param \DateTime $dateTime
     * @param DashboardView $view
     */
    private function addInfotexts(\DateTime $dateTime, DashboardView $view): void {
        $infotexts = $this->infotextRepository->findAllByDate($dateTime);

        foreach($infotexts as $infotext) {
            $view->addInfotext($infotext);
        }
    }

    private function getCurrentTimetablePeriod(DateTime $dateTime): ?TimetablePeriod {
        return $this->timetablePeriodHelper->getPeriod($dateTime);
    }

    private function computeAbsentStudents(TimetableLesson $lessonEntity, int $lesson, \DateTime $dateTime) {
        $lessonStudents = $lessonEntity
            ->getTuition()
            ->getStudyGroup()
            ->getMemberships()
            ->map(function(StudyGroupMembership $membership) {
                return $membership->getStudent();
            })
            ->toArray();

        $absentStudents = ArrayUtils::unique(
            array_merge(
                $this->absenceRepository->findAllStudentsByDateAndLesson($dateTime, $lessonStudents, $lesson),
                $this->computeExamStudents($lessonEntity, $lesson, $dateTime)
            )
        );

        // Sort the students
        $this->sorter->sort($absentStudents, AbsentStudentStrategy::class);

        return $absentStudents;
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
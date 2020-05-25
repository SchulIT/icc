<?php

namespace App\Dashboard;

use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\GradeTeacher;
use App\Entity\Message;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\AbsentStudentGroup;
use App\Grouping\Grouper;
use App\Grouping\AbsentStudentStrategy as AbstentStudentGroupStrategy;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Security\Voter\AbsenceVoter;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\MessageVoter;
use App\Security\Voter\SubstitutionVoter;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Sorting\AbsentStudentStrategy;
use App\Sorting\AbsentStudyGroupStrategy;
use App\Sorting\AbsentTeacherStrategy;
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
    private $studyGroupRepository;

    private $studyGroupHelper;
    private $timetablePeriodHelper;
    private $timetableSettings;
    private $sorter;
    private $grouper;

    private $authorizationChecker;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, ExamRepositoryInterface $examRepository,
                                TimetableLessonRepositoryInterface $timetableRepository, TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableWeekRepositoryInterface $timetableWeekRepository,
                                MessageRepositoryInterface $messageRepository, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository, StudyGroupRepositoryInterface $studyGroupRepository,
                                StudyGroupHelper $studyGroupHelper, TimetablePeriodHelper $timetablePeriodHelper, Sorter $sorter, Grouper $grouper, TimetableSettings $timetableSettings, AuthorizationCheckerInterface $authorizationChecker) {
        $this->substitutionRepository = $substitutionRepository;
        $this->examRepository = $examRepository;
        $this->timetableRepository = $timetableRepository;
        $this->timetableWeekRepository = $timetableWeekRepository;
        $this->supervisionRepository = $supervisionRepository;
        $this->messageRepository = $messageRepository;
        $this->infotextRepository = $infotextRepository;
        $this->absenceRepository = $absenceRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->studyGroupHelper = $studyGroupHelper;
        $this->timetablePeriodHelper = $timetablePeriodHelper;
        $this->timetableSettings = $timetableSettings;
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createViewForTeacher(Teacher $teacher, DateTime $dateTime, bool $includeGradeMessages = false): DashboardView {
        $view = new DashboardView();

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $dateTime, $view, true, $numberOfWeeks);
            $this->addSupervisions($this->supervisionRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher), $view);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $messages = [ ];

        if($includeGradeMessages === true) {
            /** @var GradeTeacher $gradeTeacher */
            foreach($teacher->getGrades() as $gradeTeacher) {
                $studyGroups = $this->studyGroupRepository->findAllByGrades($gradeTeacher->getGrade());
                $messages = array_merge($messages, $this->messageRepository->findBy(MessageScope::Messages(), UserType::Student(), $dateTime, $studyGroups));
            }
        }

        $messages = array_merge($messages, $this->messageRepository->findBy(MessageScope::Messages(), UserType::Teacher(), $dateTime));

        $messages = ArrayUtils::createArrayWithKeys($messages, function(Message $message) {
            return $message->getId();
        });

        $this->addMessages($messages, $view);

        if($includeGradeMessages)

        $this->addSubstitutions($this->substitutionRepository->findAllForTeacher($teacher, $dateTime), $view);
        $this->addExams($exams = $this->examRepository->findAllByTeacher($teacher, $dateTime, true), $view, $teacher);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);

        return $view;
    }

    public function createViewForStudentOrParent(Student $student, DateTime $dateTime, UserType $userType): DashboardView {
        if(!EnumArrayUtils::inArray($userType, [ UserType::Student(), UserType::Parent() ])) {
            throw new \InvalidArgumentException(sprintf('$userType must be either Student or Parent, "%s" given.', $userType->getValue()));
        }

        $view = new DashboardView();

        $studyGroups = $this->studyGroupHelper->getStudyGroups([$student])->toArray();

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndStudent($currentPeriod, $student), $dateTime, $view, false, $numberOfWeeks);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $userType, $dateTime, $studyGroups), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForStudyGroups($studyGroups, $dateTime), $view);
        $this->addExams($exams = $this->examRepository->findAllByStudents([$student], $dateTime, true), $view, null);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);

        return $view;
    }

    public function createViewForUser(User $user, DateTime $dateTime): DashboardView {
        $view = new DashboardView();

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $user->getUserType(), $dateTime), $view);

        return $view;
    }

    /**
     * @param TimetableLesson[] $lessons
     * @param DateTime $dateTime
     * @param DashboardView $dashboardView
     * @param bool $computeAbsences
     * @param int $numberOfWeeks
     */
    private function addTimetableLessons(iterable $lessons, DateTime $dateTime, DashboardView $dashboardView, bool $computeAbsences, int $numberOfWeeks): void {
        foreach($lessons as $lesson) {
            $isWeek = (int)$dateTime->format('W') % $numberOfWeeks === $lesson->getWeek()->getWeekMod();
            $isDay = (int)$dateTime->format('N') === $lesson->getDay();

            if($isWeek === false || $isDay === false) {
                continue;
            }

            $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson(), $dateTime) : [ ];

            $dashboardView->addItem($lesson->getLesson(), new TimetableLessonViewItem($lesson, $absentStudents));

            if($lesson->isDoubleLesson()) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lesson, $lesson->getLesson() + 1, $dateTime) : [ ];
                $dashboardView->addItem($lesson->getLesson() + 1, new TimetableLessonViewItem($lesson, $absentStudents));
            }
        }
    }

    private function addEmptyTimetableLessons(DashboardView $view, int $numberOfLessons) {
        $lessons = $view->getLessonNumbers();

        for($i = 1; $i <= $numberOfLessons; $i++) {
            if(!in_array($i, $lessons)) {
                $view->addItem($i, new TimetableLessonViewItem(null, []));
            }
        }

        foreach($lessons as $lessonNumber) {
            $hasLessonEntry = false;
            $lesson = $view->getLesson($lessonNumber);

            if($lesson !== null) {
                foreach ($lesson->getItems() as $item) {
                    if ($item instanceof TimetableLessonViewItem) {
                        $hasLessonEntry = true;
                        break;
                    }
                }
            } else {
                $hasLessonEntry = false;
            }

            if($hasLessonEntry === false) {
                $view->addItem($lessonNumber, new TimetableLessonViewItem(null, []));
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
     * @param Teacher|null $teacher
     */
    private function addExams(iterable $exams, DashboardView $dashboardView, ?Teacher $teacher): void {
        foreach($exams as $exam) {
            if($this->authorizationChecker->isGranted(ExamVoter::Show, $exam) !== true) {
                continue;
            }

            /** @var int[] $tuitionTeacherIds */
            $tuitionTeacherIds = [ ];

            /** @var Tuition $tuition */
            foreach($exam->getTuitions() as $tuition) {
                $tuitionTeacherIds = array_merge($tuitionTeacherIds, array_map(function(Teacher $teacher) {
                    return $teacher->getId();
                }, $tuition->getTeachers()));
            }

            $supervisions = [ ];

            if($teacher !== null) {
                /** @var ExamSupervision $supervision */
                foreach($exam->getSupervisions() as $supervision) {
                    $supervisions[$supervision->getLesson()] = $supervision->getTeacher()->getId();
                }
            }

            for($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                if($teacher !== null) {
                    if(in_array($teacher->getId(), $tuitionTeacherIds)) {
                        $dashboardView->addItem($lesson, new ExamViewItem($exam));
                    }

                    if(isset($supervisions[$lesson]) && $supervisions[$lesson] === $teacher->getId()) {
                        $dashboardView->addItem($lesson, new ExamSupervisionViewItem($exam));
                    }
                } else {
                    $dashboardView->addItem($lesson, new ExamViewItem($exam));
                }
            }
        }
    }

    /**
     * @param DateTime $dateTime
     * @param DashboardView $view
     */
    private function addInfotexts(DateTime $dateTime, DashboardView $view): void {
        $infotexts = $this->infotextRepository->findAllByDate($dateTime);

        foreach($infotexts as $infotext) {
            $view->addInfotext($infotext);
        }
    }

    private function addAbsentTeachers(array $absences, DashboardView $view): void {
        $this->sorter->sort($absences, AbsentTeacherStrategy::class);
        foreach($absences as $absence) {
            if($this->authorizationChecker->isGranted(AbsenceVoter::View, $absence)) {
                $view->addAbsence($absence);
            }
        }
    }

    private function addAbsentStudyGroup(array $absences, DashboardView $view): void {
        $this->sorter->sort($absences, AbsentStudyGroupStrategy::class);
        foreach($absences as $absence) {
            if($this->authorizationChecker->isGranted(AbsenceVoter::View, $absence)) {
                $view->addAbsence($absence);
            }
        }
    }

    private function getCurrentTimetablePeriod(DateTime $dateTime): ?TimetablePeriod {
        return $this->timetablePeriodHelper->getPeriod($dateTime);
    }

    /**
     * @param TimetableLesson $lessonEntity
     * @param int $lesson
     * @param DateTime $dateTime
     * @return AbsentStudentGroup[]
     */
    private function computeAbsentStudents(TimetableLesson $lessonEntity, int $lesson, DateTime $dateTime) {
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
                array_map(function(Student $student) {
                    return new AbsentStudent($student, AbsenceReason::Other());
                }, $this->absenceRepository->findAllStudentsByDateAndLesson($dateTime, $lessonStudents, $lesson)),
                $this->computeExamStudents($lessonEntity, $lesson, $dateTime)
            )
        );

        $groups = $this->grouper->group($absentStudents, AbstentStudentGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, AbsentStudentStrategy::class);

        return $groups;
    }

    private function computeExamStudents(TimetableLesson $lessonEntity, int $lesson, DateTime $dateTime) {
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

        $absentStudents = array_filter($absentStudents, function(AbsentExamStudent $absentStudent) use($lessonEntity) {
            $tuitionIds = $absentStudent->getExam()->getTuitions()->map(function(Tuition $tuition) {
                return $tuition->getId();
            })->toArray();
            return !in_array($lessonEntity->getTuition()->getId(), $tuitionIds);
        });

        return $absentStudents;
    }

}
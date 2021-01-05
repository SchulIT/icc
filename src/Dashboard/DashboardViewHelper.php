<?php

namespace App\Dashboard;

use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Entity\Appointment;
use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\FreeTimespan;
use App\Entity\GradeTeacher;
use App\Entity\Message;
use App\Entity\MessageScope;
use App\Entity\Room;
use App\Entity\ResourceReservation;
use App\Entity\SickNote;
use App\Entity\Student;
use App\Entity\StudyGroup;
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
use App\Grouping\AbsentStudentStrategy as AbstentStudentGroupStrategy;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\FreeTimespanRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Repository\SickNoteRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Security\Voter\AbsenceVoter;
use App\Security\Voter\AppointmentVoter;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\MessageVoter;
use App\Security\Voter\ResourceReservationVoter;
use App\Security\Voter\SubstitutionVoter;
use App\Security\Voter\TimetablePeriodVoter;
use App\Settings\DashboardSettings;
use App\Settings\TimetableSettings;
use App\Sorting\AbsentStudentStrategy;
use App\Sorting\AbsentStudyGroupStrategy;
use App\Sorting\AbsentTeacherStrategy;
use App\Sorting\MessageStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetablePeriodHelper;
use App\Timetable\TimetableTimeHelper;
use App\Timetable\TimetableWeekHelper;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use App\Utils\StudyGroupHelper;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    private $appointmentRepository;
    private $roomReservationRepository;
    private $freeTimespanRepository;

    private $studyGroupHelper;
    private $timetablePeriodHelper;
    private $timetableSettings;
    private $timetableWeekHelper;
    private $timetableTimeHelper;
    private $sorter;
    private $grouper;
    private $dashboardSettings;

    private $authorizationChecker;
    private $validator;

    private $absenceResolver;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, ExamRepositoryInterface $examRepository,
                                TimetableLessonRepositoryInterface $timetableRepository, TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableWeekRepositoryInterface $timetableWeekRepository,
                                MessageRepositoryInterface $messageRepository, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository, AppointmentRepositoryInterface $appointmentRepository, ResourceReservationRepositoryInterface $reservationRepository,
                                FreeTimespanRepositoryInterface $freeTimespanRepository,
                                StudyGroupHelper $studyGroupHelper, TimetablePeriodHelper $timetablePeriodHelper, TimetableWeekHelper $weekHelper, TimetableTimeHelper $timetableTimeHelper, Sorter $sorter, Grouper $grouper,
                                TimetableSettings $timetableSettings, DashboardSettings $dashboardSettings, AuthorizationCheckerInterface $authorizationChecker,
                                ValidatorInterface $validator, AbsenceResolver $absenceResolver) {
        $this->substitutionRepository = $substitutionRepository;
        $this->examRepository = $examRepository;
        $this->timetableRepository = $timetableRepository;
        $this->timetableWeekRepository = $timetableWeekRepository;
        $this->supervisionRepository = $supervisionRepository;
        $this->messageRepository = $messageRepository;
        $this->infotextRepository = $infotextRepository;
        $this->absenceRepository = $absenceRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->appointmentRepository = $appointmentRepository;
        $this->roomReservationRepository = $reservationRepository;
        $this->freeTimespanRepository = $freeTimespanRepository;
        $this->studyGroupHelper = $studyGroupHelper;
        $this->timetablePeriodHelper = $timetablePeriodHelper;
        $this->timetableSettings = $timetableSettings;
        $this->timetableWeekHelper = $weekHelper;
        $this->timetableTimeHelper = $timetableTimeHelper;
        $this->sorter = $sorter;
        $this->grouper = $grouper;
        $this->dashboardSettings = $dashboardSettings;
        $this->authorizationChecker = $authorizationChecker;
        $this->validator = $validator;
        $this->absenceResolver = $absenceResolver;
    }

    public function createViewForRoom(Room $room, DateTime $dateTime): DashboardView {
        $view = new DashboardView($dateTime);

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndRoom($currentPeriod, $room), $dateTime, $view, true, $numberOfWeeks);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $this->addSubstitutions($this->substitutionRepository->findAllForRooms([ $room ], $dateTime), $view, true);
        $this->addExams($exams = $this->examRepository->findAllByRoomAndDate($room, $dateTime), $view, null, true);
        $this->addRoomReservations($this->roomReservationRepository->findAllByResourceAndDate($room, $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);

        return $view;
    }

    public function createViewForTeacher(Teacher $teacher, DateTime $dateTime, bool $includeGradeMessages = false): DashboardView {
        $view = new DashboardView($dateTime);

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

        $this->addSubstitutions($this->substitutionRepository->findAllForTeacher($teacher, $dateTime), $view, true);
        $this->addExams($exams = $this->examRepository->findAllByTeacher($teacher, $dateTime, true), $view, $teacher, true);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);
        $this->addAppointments($this->appointmentRepository->findAllForTeacher($teacher, $dateTime), $view);
        $this->addRoomReservations($this->roomReservationRepository->findAllByTeacherAndDate($teacher, $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);

        return $view;
    }

    public function createViewForStudentOrParent(Student $student, DateTime $dateTime, UserType $userType): DashboardView {
        if(!EnumArrayUtils::inArray($userType, [ UserType::Student(), UserType::Parent() ])) {
            throw new \InvalidArgumentException(sprintf('$userType must be either Student or Parent, "%s" given.', $userType->getValue()));
        }

        $view = new DashboardView($dateTime);

        $studyGroups = $this->studyGroupHelper->getStudyGroups([$student])->toArray();

        $currentPeriod = $this->getCurrentTimetablePeriod($dateTime);
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        if($currentPeriod !== null) {
            $this->addTimetableLessons($this->timetableRepository->findAllByPeriodAndStudent($currentPeriod, $student), $dateTime, $view, false, $numberOfWeeks);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages(), $userType, $dateTime, $studyGroups), $view);
        $this->addSubstitutions($this->substitutionRepository->findAllForStudyGroups($studyGroups, $dateTime), $view, false);
        $this->addExams($exams = $this->examRepository->findAllByStudents([$student], $dateTime, true), $view, null, false);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);
        $this->addAppointments($this->appointmentRepository->findAllForStudents([$student], $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);

        return $view;
    }

    public function createViewForUser(User $user, DateTime $dateTime): DashboardView {
        $view = new DashboardView($dateTime);

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
            if($this->authorizationChecker->isGranted(TimetablePeriodVoter::View, $lesson->getPeriod()) !== true) {
                continue;
            }

            $isWeek = (int)$dateTime->format('W') % $numberOfWeeks === $lesson->getWeek()->getWeekMod();
            $isDay = (int)$dateTime->format('N') === $lesson->getDay();

            if($isWeek === false || $isDay === false) {
                continue;
            }

            $lessonStudents = $lesson
                ->getTuition()
                ->getStudyGroup()
                ->getMemberships()
                ->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                })
                ->toArray();

            $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lessonStudents, $lesson->getLesson(), $dateTime) : [ ];

            $dashboardView->addItem($lesson->getLesson(), new TimetableLessonViewItem($lesson, $absentStudents));

            if($lesson->isDoubleLesson()) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lessonStudents, $lesson->getLesson() + 1, $dateTime) : [ ];
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
        $dayOfWeek = (int)$dashboardView->getDateTime()->format('w'); // PHP gives the day of week 0-based

        foreach($supervisions as $supervision) {
            if($this->authorizationChecker->isGranted(TimetablePeriodVoter::View, $supervision->getPeriod()) !== true) {
                continue;
            }

            if($supervision->getDay() === $dayOfWeek && $this->timetableWeekHelper->isTimetableWeek($dashboardView->getDateTime(), $supervision->getWeek())) {
                if ($supervision->isBefore()) {
                    $dashboardView->addItemBefore($supervision->getLesson(), new SupervisionViewItem($supervision));
                } else {
                    $dashboardView->addItem($supervision->getLesson(), new SupervisionViewItem($supervision));
                }
            }
        }
    }

    /**
     * @param Substitution[] $substitutions
     * @param DashboardView $dashboardView
     * @param bool $computeAbsences
     */
    private function addSubstitutions(iterable $substitutions, DashboardView $dashboardView, bool $computeAbsences): void {
        $freeTypes = $this->dashboardSettings->getFreeLessonSubstitutionTypes();

        foreach($substitutions as $substitution) {
            if($this->authorizationChecker->isGranted(SubstitutionVoter::View, $substitution) !== true) {
                continue;
            }

            $isFreeLesson = in_array($substitution->getType(), $freeTypes);

            if($substitution->startsBefore()) {
                $dashboardView->addItemBefore($substitution->getLessonStart(), new SubstitutionViewItem($substitution, $isFreeLesson, [ ], [ ]));

                if($substitution->getLessonEnd() - $substitution->getLessonStart() === 0) {
                    // Do not expand more lessons when the end is the same lesson as the beginning
                    continue;
                }
            }

            $studyGroups = $substitution->getReplacementStudyGroups()->count() > 0 ? $substitution->getReplacementStudyGroups() : $substitution->getStudyGroups();
            $students = $this->getStudents($studyGroups);

            for ($lesson = $substitution->getLessonStart(); $lesson <= $substitution->getLessonEnd(); $lesson++) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($students, $lesson, $substitution->getDate()) : [ ];
                $dashboardView->addItem($lesson, new SubstitutionViewItem($substitution, $isFreeLesson, $students, $absentStudents));
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
     * @param bool $computeAbsences
     */
    private function addExams(iterable $exams, DashboardView $dashboardView, ?Teacher $teacher, bool $computeAbsences): void {
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
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($exam->getStudents()->toArray(), $lesson, $exam->getDate(), [ ExamStudentsResolver::class ]) : [ ];

                if($teacher !== null) {
                    if(in_array($teacher->getId(), $tuitionTeacherIds)) {
                        $dashboardView->addItem($lesson, new ExamViewItem($exam, $absentStudents));
                    }

                    if(isset($supervisions[$lesson]) && $supervisions[$lesson] === $teacher->getId()) {
                        $dashboardView->addItem($lesson, new ExamSupervisionViewItem($exam));
                    }
                } else {
                    $dashboardView->addItem($lesson, new ExamViewItem($exam, $absentStudents));
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

    /**
     * @param Appointment[] $appointments
     * @param DashboardView $view
     */
    private function addAppointments(array $appointments, DashboardView $view): void {
        $freeCategories = $this->timetableSettings->getCategoryIds();

        foreach($appointments as $appointment) {
            if($this->authorizationChecker->isGranted(AppointmentVoter::View, $appointment)) {
                if(in_array($appointment->getCategory()->getId(), $freeCategories)) {
                    $view->removeLessons();;
                }

                $view->addAppointment($appointment);
            }
        }
    }

    /**
     * @param ResourceReservation[] $reservations
     * @param DashboardView $view
     */
    private function addRoomReservations(array $reservations, DashboardView $view): void {
        foreach($reservations as $reservation) {
            if($this->authorizationChecker->isGranted(ResourceReservationVoter::View)) {
                $violations = $this->validator->validate($reservation, null, ['collision']);

                for($lessonNumber = $reservation->getLessonStart(); $lessonNumber <= $reservation->getLessonEnd(); $lessonNumber++) {
                    $view->addItem($lessonNumber, new RoomReservationViewItem($reservation, $violations));
                }
            }
        }
    }

    /**
     * @param FreeTimespan[] $timespans
     * @param DashboardView $view
     */
    private function addFreeTimespans(array $timespans, DashboardView $view): void {
         foreach($timespans as $timespan) {
            for($lessonNumber = $timespan->getStart(); $lessonNumber <= $timespan->getEnd(); $lessonNumber++) {
                $view->addItem($lessonNumber, new FreeLessonView());
            }
        }
    }

    private function getCurrentTimetablePeriod(DateTime $dateTime): ?TimetablePeriod {
        return $this->timetablePeriodHelper->getPeriod($dateTime);
    }

    /**
     * @param Student[] $students
     * @param int $lesson
     * @param DateTime $dateTime
     * @param string[] FQCN of excluded strategies
     * @return AbsentStudentGroup[]
     */
    private function computeAbsentStudents(array $students, int $lesson, DateTime $dateTime, array $excludedResolvers = [ ]): array {
        $absentStudents = $this->absenceResolver->resolve($dateTime, $lesson, $students, $excludedResolvers);

        /** @var AbsentStudentGroup[] $groups */
        $groups = $this->grouper->group($absentStudents, AbstentStudentGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, AbsentStudentStrategy::class);

        return $groups;
    }

    /**
     * @param iterable|StudyGroup[] $studyGroups
     * @return Student[]
     */
    private function getStudents(iterable $studyGroups): array {
        $students = [ ];

        foreach($studyGroups as $group) {
            /** @var StudyGroupMembership $membership */
            foreach($group->getMemberships() as $membership) {
                $students[] = $membership->getStudent();
            }
        }

        return $students;
    }

}
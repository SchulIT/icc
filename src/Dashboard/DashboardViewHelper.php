<?php

namespace App\Dashboard;

use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Entity\Appointment;
use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\ExamSupervision;
use App\Entity\FreeTimespan;
use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\Message;
use App\Entity\MessageScope;
use App\Entity\ResourceReservation;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\AbsentStudentGroup;
use App\Grouping\AbsentStudentStrategy as AbstentStudentGroupStrategy;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\BookStudentInformationRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\FreeTimespanRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\ParentsDayAppointmentRepositoryInterface;
use App\Repository\ParentsDayRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TeacherAbsenceCommentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonAdditionalInformationRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\AbsenceVoter;
use App\Security\Voter\AppointmentVoter;
use App\Security\Voter\BookStudentInformationVoter;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\MessageVoter;
use App\Security\Voter\ResourceReservationVoter;
use App\Security\Voter\SubstitutionVoter;
use App\Settings\BookSettings;
use App\Settings\DashboardSettings;
use App\Settings\TimetableSettings;
use App\Sorting\AbsentRoomStrategy;
use App\Sorting\AbsentStudentStrategy;
use App\Sorting\AbsentStudyGroupStrategy;
use App\Sorting\AbsentTeacherStrategy;
use App\Sorting\MessageStrategy;
use App\Sorting\ParentsDayAppointmentStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableTimeHelper;
use App\Utils\ArrayUtils;
use App\Utils\StudyGroupHelper;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DashboardViewHelper {

    public function __construct(private SubstitutionRepositoryInterface $substitutionRepository, private ExamRepositoryInterface $examRepository, private TimetableLessonRepositoryInterface $timetableRepository,
                                private TimetableSupervisionRepositoryInterface $supervisionRepository, private MessageRepositoryInterface $messageRepository, private InfotextRepositoryInterface $infotextRepository,
                                private AbsenceRepositoryInterface $absenceRepository, private StudyGroupRepositoryInterface $studyGroupRepository, private AppointmentRepositoryInterface $appointmentRepository,
                                private ResourceReservationRepositoryInterface $roomReservationRepository, private FreeTimespanRepositoryInterface $freeTimespanRepository, private StudyGroupHelper $studyGroupHelper,
                                private TimetableTimeHelper $timetableTimeHelper, private Sorter $sorter, private Grouper $grouper, private TimetableSettings $timetableSettings, private DashboardSettings $dashboardSettings,
                                private AuthorizationCheckerInterface $authorizationChecker, private ValidatorInterface $validator, private DateHelper $dateHelper, private AbsenceResolver $absenceResolver,
                                private SectionResolverInterface $sectionResolver, private readonly TuitionRepositoryInterface $tuitionRepository, private readonly TimetableLessonAdditionalInformationRepositoryInterface $timetableLessonAdditionalInformationRepository,
                                private readonly BookSettings $bookSettings, private readonly LessonEntryRepositoryInterface $lessonEntryRepository, private readonly TeacherRepositoryInterface $teacherRepository,
                                private readonly StudentRepositoryInterface $studentRepository, private readonly TokenStorageInterface $tokenStorage,
                                private readonly ParentsDayRepositoryInterface $parentsDayRepository, private readonly ParentsDayAppointmentRepositoryInterface $parentsDayAppointmentRepository,
                                private readonly BookStudentInformationRepositoryInterface $bookStudentInformationRepository)
    {
    }

    private function getTimetableStartDate(): ?DateTime {
        $user = $this->tokenStorage->getToken()?->getUser();

        if(!$user instanceof User) {
            return null;
        }

        return $this->timetableSettings->getStartDate($user->getUserType());
    }

    private function getTimetableEndDate(): ?DateTime {
        $user = $this->tokenStorage->getToken()?->getUser();

        if(!$user instanceof User) {
            return null;
        }

        return $this->timetableSettings->getEndDate($user->getUserType());
    }

    public function createViewForRoom(Room $room, DateTime $dateTime): DashboardView {
        $view = new DashboardView($dateTime);

        $start = $this->getTimetableStartDate();
        $end = $this->getTimetableEndDate();

        if($start !== null && $end !== null && $this->dateHelper->isBetween($dateTime, $start, $end)) {
            $this->addTimetableLessons($this->timetableRepository->findAllByRoom($dateTime, $dateTime, $room), $dateTime, $view, true);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $this->addSubstitutions($this->substitutionRepository->findAllForRooms([ $room ], $dateTime), $view, true);
        $this->addExams($exams = $this->examRepository->findAllByRoomAndDate($room, $dateTime), $view, null, true);
        $this->addRoomReservations($this->roomReservationRepository->findAllByResourceAndDate($room, $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);
        $this->setCurrentLesson($view);

        return $view;
    }

    public function createViewForTeacher(Teacher $teacher, DateTime $dateTime, bool $includeGradeMessages = false): DashboardView {
        $view = new DashboardView($dateTime);

        $start = $this->getTimetableStartDate();
        $end = $this->getTimetableEndDate();

        if($start !== null && $end !== null && $this->dateHelper->isBetween($dateTime, $start, $end)) {
            $this->addTimetableLessons($this->timetableRepository->findAllByTeacher($dateTime, $dateTime, $teacher), $dateTime, $view, true);
            $this->addSupervisions($this->supervisionRepository->findAllByTeacher($dateTime, $dateTime, $teacher), $view);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $messages = [ ];

        $section = $this->sectionResolver->getSectionForDate($dateTime);

        if($includeGradeMessages === true && $section !== null) {
            /** @var GradeTeacher $gradeTeacher */
            foreach($teacher->getGrades() as $gradeTeacher) {
                $studyGroups = $this->studyGroupRepository->findAllByGrades($gradeTeacher->getGrade(), $section);
                $messages = array_merge($messages, $this->messageRepository->findBy(MessageScope::Messages, UserType::Parent, $dateTime, $studyGroups));
            }
        }

        $messages = array_merge($messages, $this->messageRepository->findBy(MessageScope::Messages, UserType::Teacher, $dateTime));

        $messages = ArrayUtils::createArrayWithKeys($messages, fn(Message $message) => $message->getId());

        $this->addMessages($messages, $view);

        $this->addSubstitutions($this->substitutionRepository->findAllForTeacher($teacher, $dateTime), $view, true, $teacher);
        $this->addExams($this->examRepository->findAllByTeacher($teacher, $dateTime, true), $view, $teacher, true);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);
        $this->addAbsentRooms($this->absenceRepository->findAllRooms($dateTime), $view);
        $this->addAppointments($this->appointmentRepository->findAllForTeacher($teacher, $dateTime), $view);
        $this->addRoomReservations($this->roomReservationRepository->findAllByTeacherAndDate($teacher, $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);
        $this->setCurrentLesson($view);
        $this->addBirthdays($view, $dateTime);

        $appointments = [ ];
        foreach($this->parentsDayRepository->findByDate($dateTime) as $parentsDay) {
            $appointments = array_merge($appointments, $this->parentsDayAppointmentRepository->findForTeacher($teacher, $parentsDay));
        }

        $this->addParentsDayAppointments($view, $appointments);

        return $view;
    }

    public function createViewForStudentOrParent(Student $student, DateTime $dateTime, UserType $userType, User|null $user = null): DashboardView {
        $view = new DashboardView($dateTime);

        $studyGroups = $this->studyGroupHelper->getStudyGroups([$student])->toArray();

        $start = $this->getTimetableStartDate();
        $end = $this->getTimetableEndDate();

        if($start !== null && $end !== null && $this->dateHelper->isBetween($dateTime, $start, $end)) {
            $this->addTimetableLessons($this->timetableRepository->findAllByStudent($dateTime, $dateTime, $student), $dateTime, $view, false);
            $this->addEmptyTimetableLessons($view, $this->timetableSettings->getMaxLessons());
        }

        $section = $this->sectionResolver->getSectionForDate($dateTime);

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages, $userType, $dateTime, $studyGroups), $view);
        $this->addSubstitutions($this->filterSubstitutionsByGrade($this->substitutionRepository->findAllForStudyGroups($studyGroups, $dateTime), $student->getGrade($section)), $view, false);
        $this->addExams($this->examRepository->findAllByStudents([$student], $dateTime, true), $view, null, false);
        $this->addInfotexts($dateTime, $view);
        $this->addAbsentStudyGroup($this->absenceRepository->findAllStudyGroups($dateTime), $view);
        $this->addAbsentTeachers($this->absenceRepository->findAllTeachers($dateTime), $view);
        $this->addAppointments($this->appointmentRepository->findAllForStudents([$student], $dateTime), $view);
        $this->addFreeTimespans($this->freeTimespanRepository->findAllByDate($dateTime), $view);
        $this->setCurrentLesson($view);

        $this->addExercises($view, $student, $dateTime);

        $appointments = [ ];
        foreach($this->parentsDayRepository->findByDate($dateTime) as $parentsDay) {
            $appointments = array_merge(
                $appointments,
                $this->parentsDayAppointmentRepository->findForStudents(
                    $user !== null ? $user->getStudents()->toArray() : [$student ],
                    $parentsDay
                )
            );
        }

        $this->addParentsDayAppointments($view, $appointments);

        return $view;
    }

    public function createViewForUser(User $user, DateTime $dateTime): DashboardView {
        $view = new DashboardView($dateTime);

        $this->addMessages($this->messageRepository->findBy(MessageScope::Messages, $user->getUserType(), $dateTime), $view);
        $this->setCurrentLesson($view);
        $this->addBirthdays($view, $dateTime);

        return $view;
    }

    private function addBirthdays(DashboardView $view, DateTime $dateTime): DashboardView {
        foreach($this->teacherRepository->findAllByBirthday($dateTime) as $birthdayTeacher) {
            if($this->authorizationChecker->isGranted('ROLE_SHOW_BIRTHDAY', $birthdayTeacher)) {
                $view->addTeacherBirthday($birthdayTeacher);
            }
        }

        foreach($this->studentRepository->findAllByBirthday($dateTime) as $birthdayStudent) {
            if($this->authorizationChecker->isGranted('ROLE_SHOW_BIRTHDAY', $birthdayStudent)) {
                $view->addStudentBirthday($birthdayStudent);
            }
        }

        return $view;
    }

    private function setCurrentLesson(DashboardView $dashboardView): void {
        foreach($dashboardView->getLessons() as $lesson) {
            $startTime = $this->timetableTimeHelper->getLessonStartDateTime($dashboardView->getDateTime(), $lesson->getLessonNumber());
            $endTime = $this->timetableTimeHelper->getLessonEndDateTime($dashboardView->getDateTime(), $lesson->getLessonNumber());
            $now = $this->dateHelper->getNow();

            if($startTime <= $now && $now <= $endTime) {
                $lesson->setIsCurrent(true);
            }
        }
    }

    private function addParentsDayAppointments(DashboardView $view, array $appointments): void {
        if(count($appointments) === 0) {
            return;
        }

        $this->sorter->sort($appointments, ParentsDayAppointmentStrategy::class);
        foreach($appointments as $appointment) {
            $view->addParentsDayAppointment($appointment);
        }
    }

    private function addExercises(DashboardView $view, Student $student, DateTime $date) {
        $start = (clone $date)->modify(sprintf('-%d days', $this->bookSettings->getExercisesDays()));
        $end = clone $date;
        $section = $this->sectionResolver->getSectionForDate($date);
        $grade = $student->getGrade($section);

        if($grade === null) {
            return;
        }

        $exerciseView = new ExercisesView($start, $end, $grade);
        $exerciseView->setEntriesWithExercises($this->lessonEntryRepository->findAllByStudentWithExercises($student, $start, $end));
        $view->setExercises($exerciseView);
    }

    /**
     * @param TimetableLesson[] $lessons
     */
    private function addTimetableLessons(iterable $lessons, DateTime $dateTime, DashboardView $dashboardView, bool $computeAbsences): void {
        foreach($lessons as $lesson) {
            $lessonStudents = [ ];
            $studentInfo = [ ];

            if($lesson->getTuition() !== null && $lesson->getTuition()->getStudyGroup() !== null) {
                foreach($this->bookStudentInformationRepository->findByStudyGroup($lesson->getTuition()->getStudyGroup(), $lesson->getDate(), $lesson->getDate()) as $info) {
                    if($this->authorizationChecker->isGranted(BookStudentInformationVoter::Show, $info)) {
                        $studentInfo[] = $info;
                    }
                }
            }

            if($lesson->getTuition() !== null) {
                $lessonStudents = $lesson
                    ->getTuition()
                    ->getStudyGroup()
                    ->getMemberships()
                    ->map(fn(StudyGroupMembership $membership) => $membership->getStudent())
                    ->toArray();
            }

            for($lessonNumber = $lesson->getLessonStart(); $lessonNumber <= $lesson->getLessonEnd(); $lessonNumber++) {
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($lessonStudents, $lessonNumber, $dateTime, [], $lesson->getTuition()) : [ ];
                $additionalInfo = [ ];
                if($lesson->getTuition()?->getStudyGroup() !== null) {
                    $additionalInfo = $this->timetableLessonAdditionalInformationRepository->findBy($lesson->getDate(), $lesson->getTuition()?->getStudyGroup(), $lessonNumber);
                }
                $dashboardView->addItem($lessonNumber, new TimetableLessonViewItem($lesson, $absentStudents, $studentInfo, $additionalInfo));
            }
        }
    }

    private function addEmptyTimetableLessons(DashboardView $view, int $numberOfLessons): void {
        $lessons = $view->getLessonNumbers();

        for($i = 1; $i <= $numberOfLessons; $i++) {
            if(!in_array($i, $lessons)) {
                $view->addItem($i, new TimetableLessonViewItem(null, [], [],  []));
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
                $view->addItem($lessonNumber, new TimetableLessonViewItem(null, [], [], []));
            }
        }
    }

    /**
     * @param TimetableSupervision[] $supervisions
     */
    private function addSupervisions(iterable $supervisions, DashboardView $dashboardView): void {
        foreach($supervisions as $supervision) {
            if ($supervision->isBefore()) {
                $dashboardView->addItemBefore($supervision->getLesson(), new SupervisionViewItem($supervision));
            } else {
                $dashboardView->addItem($supervision->getLesson(), new SupervisionViewItem($supervision));
            }
        }
    }

    /**
     * @param Substitution[] $substitutions
     */
    private function addSubstitutions(iterable $substitutions, DashboardView $dashboardView, bool $computeAbsences, ?Teacher $teacher = null): void {
        $freeTypes = $this->dashboardSettings->getFreeLessonSubstitutionTypes();

        foreach($substitutions as $substitution) {
            if($this->authorizationChecker->isGranted(SubstitutionVoter::View, $substitution) !== true) {
                continue;
            }

            $isFreeLesson = in_array($substitution->getType(), $freeTypes);

            if($substitution->startsBefore()) {
                $dashboardView->addItemBefore($substitution->getLessonStart(), new SubstitutionViewItem($substitution, $isFreeLesson, [ ], [ ], [ ], null, []));

                if($substitution->getLessonEnd() - $substitution->getLessonStart() === 0) {
                    // Do not expand more lessons when the end is the same lesson as the beginning
                    continue;
                }
            }
            $studyGroups = $substitution->getReplacementStudyGroups()->count() > 0 ? $substitution->getReplacementStudyGroups() : $substitution->getStudyGroups();
            $students = $this->getStudents($studyGroups);

            for ($lesson = $substitution->getLessonStart(); $lesson <= $substitution->getLessonEnd(); $lesson++) {
                $tuition = $this->tuitionRepository->findOneBySubstitution($substitution, $this->sectionResolver->getSectionForDate($substitution->getDate()));

                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($students, $lesson, $substitution->getDate(), [], $tuition) : [ ];
                $timetableLesson = $this->findTimetableLesson($substitution, $lesson);
                $additionalInfo = [ ];

                if($substitution->getStudyGroups()->count() === 1 || $substitution->getReplacementStudyGroups()->count() === 1) {
                    $studyGroup = $substitution->getReplacementStudyGroups()->count() === 1 ? $substitution->getReplacementStudyGroups()->first() : $substitution->getStudyGroups()->first();

                    if($studyGroup !== false && $studyGroup !== null) {
                        $additionalInfo = $this->timetableLessonAdditionalInformationRepository->findBy($substitution->getDate(), $studyGroup, $lesson);
                    }
                }

                $studentInfo = [ ];

                foreach($this->bookStudentInformationRepository->findByStudents($students, $substitution->getDate(), $substitution->getDate()) as $info) {
                    if($this->authorizationChecker->isGranted(BookStudentInformationVoter::Show, $info)) {
                        $studentInfo[] = $info;
                    }
                }

                $dashboardView->addItem($lesson, new SubstitutionViewItem($substitution, $isFreeLesson, $students, $absentStudents, $studentInfo, $timetableLesson, $additionalInfo));

                if($teacher !== null && $substitution->getTeachers()->contains($teacher) && $substitution->getReplacementTeachers()->count() > 0 && !$substitution->getReplacementTeachers()->contains($teacher)) {
                    $dashboardView->addAdditionalItem($lesson, new SubstitutionViewItem($substitution, false, $students, $absentStudents, $studentInfo, $timetableLesson, $additionalInfo));
                }
            }
        }
    }

    private function findTimetableLesson(Substitution $substitution, int $lesson): ?TimetableLesson {
        $tuition = $this->tuitionRepository->findOneBySubstitution($substitution, $this->sectionResolver->getSectionForDate($substitution->getDate()));

        if($tuition === null) {
            return null;
        }

        $lessons = $this->timetableRepository->findAllByTuitions($substitution->getDate(), $substitution->getDate(), [ $tuition ]);

        foreach($lessons as $timetableLesson) {
            if($timetableLesson->getLessonStart() <= $lesson && $lesson <= $timetableLesson->getLessonEnd()) {
                return $timetableLesson;
            }
        }

        return null;
    }

    /**
     * @param Message[] $messages
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
                $tuitionTeacherIds = array_merge($tuitionTeacherIds, array_map(fn(Teacher $teacher) => $teacher->getId(), $tuition->getTeachers()->toArray()));
            }

            $supervisions = [ ];

            if($teacher !== null) {
                /** @var ExamSupervision $supervision */
                foreach($exam->getSupervisions() as $supervision) {
                    $supervisions[$supervision->getLesson()] = $supervision->getTeacher()->getId();
                }
            }

            for($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                $examStudents = $exam->getStudents()->map(fn(ExamStudent $student) => $student->getStudent())->toArray();
                $absentStudents = $computeAbsences ? $this->computeAbsentStudents($examStudents, $lesson, $exam->getDate(), [ ExamStudentsResolver::class ]) : [ ];
                $studentInfo = [];

                foreach($this->bookStudentInformationRepository->findByStudents($examStudents, $exam->getDate(), $exam->getDate()) as $info) {
                    if($this->authorizationChecker->isGranted(BookStudentInformationVoter::Show, $info)) {
                        $studentInfo[] = $info;
                    }
                }

                if($teacher !== null) {
                    if(in_array($teacher->getId(), $tuitionTeacherIds)) {
                        $dashboardView->addItem($lesson, new ExamViewItem($exam, $absentStudents, $studentInfo));
                    }

                    if(isset($supervisions[$lesson]) && $supervisions[$lesson] === $teacher->getId()) {
                        $dashboardView->addItem($lesson, new ExamSupervisionViewItem($exam, $absentStudents, $studentInfo));
                    }
                } else {
                    $dashboardView->addItem($lesson, new ExamViewItem($exam, $absentStudents, $studentInfo));
                }
            }
        }
    }

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

    private function addAbsentRooms(array $absences, DashboardView $view): void {
        $this->sorter->sort($absences, AbsentRoomStrategy::class);
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
     */
    private function addFreeTimespans(array $timespans, DashboardView $view): void {
         foreach($timespans as $timespan) {
            for($lessonNumber = $timespan->getStart(); $lessonNumber <= $timespan->getEnd(); $lessonNumber++) {
                $view->addItem($lessonNumber, new FreeLessonView());
            }
        }
    }

    /**
     * @param Student[] $students
     * @param string[] $excludedResolvers FQCN of excluded strategies
     * @param Tuition|null $tuition If set, exams with this tuition are removed automatically
     * @return AbsentStudentGroup[]
     */
    private function computeAbsentStudents(array $students, int $lesson, DateTime $dateTime, array $excludedResolvers = [ ], ?Tuition $tuition = null): array {
        $absentStudents = $this->absenceResolver->resolve($dateTime, $lesson, $students, $excludedResolvers);

        if($tuition !== null) {
            $absentStudents = array_filter(
                $absentStudents,
                fn(AbsentStudent $absentStudent) => !$absentStudent instanceof AbsentExamStudent || $absentStudent->getTuition()?->getId() !== $tuition->getId()
            );
        }

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

    /**
     * Filters the given substitutions for only those which are applied to the given grade.
     *
     * @param Substitution[] $substitutions
     * @return Substitution[]
     */
    private function filterSubstitutionsByGrade(array $substitutions, ?Grade $grade) {
        $result = [ ];

        foreach($substitutions as $substitution) {
            if($substitution->getReplacementGrades()->count() === 0 || $substitution->getReplacementGrades()->contains($grade)) {
                $result[] = $substitution;
            }
        }

        return $result;
    }
}
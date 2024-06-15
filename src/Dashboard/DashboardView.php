<?php

namespace App\Dashboard;

use App\Entity\Absence;
use App\Entity\Appointment;
use App\Entity\Exam;
use App\Entity\Infotext;
use App\Entity\LessonEntry;
use App\Entity\Message;
use App\Entity\MessagePriority;
use App\Entity\ParentsDayAppointment;
use App\Entity\Student;
use App\Entity\Substitution;
use App\Entity\Teacher;
use DateTime;

class DashboardView {

    /** @var Message[] */
    private array $messages = [ ];

    /** @var Infotext[] */
    private array $infotexts = [ ];

    /** @var Absence[] */
    private array $absentTeachers = [ ];

    /** @var Absence[] */
    private array $absentStudyGroups = [ ];

    /** @var Absence[] */
    private array $absentRooms = [ ];

    /** @var DashboardLesson[] */
    private array $lessons = [ ];

    /** @var DashboardLesson[] */
    private array $beforeLessons = [ ];

    /** @var Message[] */
    private array $priorityMessages = [ ];

    /** @var SubstitutionViewItem[] */
    private array $substitutionMentions = [ ];

    /** @var ExamViewItem[] */
    private array $exams = [ ];

    /** @var Appointment[] */
    private array $appointments = [ ];

    private array $teacherBirthdays = [ ];

    private array $studentBirthdays = [ ];

    private array $parentsDayAppointments = [ ];

    private ?ExercisesView $exercises = null;

    public function __construct(private DateTime $dateTime)
    {
    }

    public function getDateTime(): DateTime {
        return $this->dateTime;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    public function getPriorityMessages(): array {
        return $this->priorityMessages;
    }

    /**
     * @return DashboardLesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    /**
     * @return int[]
     */
    public function getLessonNumbers(): array {
        $lessons = [...array_keys($this->lessons), ...array_keys($this->beforeLessons)];
        sort($lessons, SORT_NUMERIC);

        return array_unique($lessons);
    }

    /**
     * @return DashboardLesson[]
     */
    public function getBeforeLessons(): array {
        return $this->beforeLessons;
    }

    public function getLesson(int $lessonNumber, bool $before = false): ?DashboardLesson {
        if($before === true) {
            return $this->beforeLessons[$lessonNumber] ?? null;
        }

        return $this->lessons[$lessonNumber] ?? null;
    }

    /**
     * @return Infotext[]
     */
    public function getInfotexts(): array {
        return $this->infotexts;
    }

    /**
     * @return Absence[]
     */
    public function getAbsentTeachers(): array {
        return $this->absentTeachers;
    }

    /**
     * @return Absence[]
     */
    public function getAbsentStudyGroups(): array {
        return $this->absentStudyGroups;
    }

    /**
     * @return Absence[]
     */
    public function getAbsentRooms(): array {
        return $this->absentRooms;
    }

    public function addItem(int $lessonNumber, AbstractViewItem $item): void {
        if(!isset($this->lessons[$lessonNumber])) {
            $this->lessons[$lessonNumber] = new DashboardLesson($lessonNumber, false);
        }

        $this->lessons[$lessonNumber]->addItem($item);
    }

    public function addItemBefore(int $lessonNumber, AbstractViewItem $item): void {
        if(!isset($this->beforeLessons[$lessonNumber])) {
            $this->beforeLessons[$lessonNumber] = new DashboardLesson($lessonNumber, true);
        }

        $this->beforeLessons[$lessonNumber]->addItem($item);
    }

    public function addMessage(Message $message): void {
        if($message->getPriority() === MessagePriority::Normal) {
            $this->messages[] = $message;
        } else {
            $this->priorityMessages[] = $message;
        }
    }

    public function addInfotext(Infotext $infotext): void {
        $this->infotexts[] = $infotext;
    }

    public function addAbsence(Absence $absence): void {
        if($absence->getStudyGroup() !== null) {
            $this->absentStudyGroups[] = $absence;
        } else if($absence->getTeacher() !== null) {
            $this->absentTeachers[] = $absence;
        } else if($absence->getRoom() !== null) {
            $this->absentRooms[] = $absence;
        }
    }

    public function addExam(ExamViewItem $exam): void {
        if(!in_array($exam, $this->exams)) {
            $this->exams[] = $exam;
        }
    }

    /**
     * @return ExamViewItem[]
     */
    public function getExams(): array {
        return $this->exams;
    }

    public function clearExams(): void {
        $this->exams = [ ];
    }

    public function addSubstitutonMention(SubstitutionViewItem $substitution): void {
        if(!in_array($substitution, $this->substitutionMentions)) {
            $this->substitutionMentions[] = $substitution;
        }
    }

    /**
     * @return SubstitutionViewItem[]
     */
    public function getSubstitutionMentions(): array {
        return $this->substitutionMentions;
    }

    public function clearSubstitutionMentions(): void {
        $this->substitutionMentions = [ ];
    }

    public function getNumberOfCollisions(): int {
        $collisions = 0;

        foreach($this->lessons as $lesson) {
            if($lesson->hasWarning()) {
                $collisions++;
            }
        }

        foreach($this->beforeLessons as $lesson) {
            if($lesson->hasWarning()) {
                $collisions++;
            }
        }

        return $collisions;
    }

    public function addAppointment(Appointment $appointment): void {
        $this->appointments[] = $appointment;
    }

    /**
     * @return Appointment[]
     */
    public function getAppointments(): array {
        return $this->appointments;
    }

    /**
     * @return ExercisesView|null
     */
    public function getExercises(): ?ExercisesView {
        return $this->exercises;
    }

    /**
     * @param ExercisesView|null $exercises
     */
    public function setExercises(?ExercisesView $exercises): void {
        $this->exercises = $exercises;
    }

    /**
     * Removes all timetable and supervisions from the dashboard view
     * (because the day is free)
     */
    public function removeLessons(): void {
        foreach($this->getLessons() as $idx => $lesson) {
            $lesson->removeLessons();

            if(count($lesson->getItems()) === 0) {
                unset($this->lessons[$idx]);
            }
        }

        foreach($this->getBeforeLessons() as $idx => $lesson) {
            $lesson->removeLessons();

            if(count($lesson->getItems()) === 0) {
                unset($this->beforeLessons[$idx]);
            }
        }
    }

    public function addStudentBirthday(Student $student): void {
        $this->studentBirthdays[] = $student;
    }

    /**
     * @return Student[]
     */
    public function getStudentBirthdays(): array {
        return $this->studentBirthdays;
    }

    public function addTeacherBirthday(Teacher $teacher): void {
        $this->teacherBirthdays[] = $teacher;
    }

    /**
     * @return array
     */
    public function getTeacherBirthdays(): array {
        return $this->teacherBirthdays;
    }

    public function addParentsDayAppointment(ParentsDayAppointment $appointment): void {
        $this->parentsDayAppointments[] = $appointment;
    }

    /**
     * @return ParentsDayAppointment[]
     */
    public function getParentsDayAppointments(): array {
        return $this->parentsDayAppointments;
    }

    public function isEmpty(): bool {
        return count($this->messages) === 0
            && count($this->infotexts) === 0
            && count($this->lessons) === 0
            && count($this->beforeLessons) === 0
            && count($this->absentStudyGroups) === 0
            && count($this->absentTeachers) === 0
            && count($this->absentRooms) === 0
            && count($this->priorityMessages) === 0
            && count($this->exams) === 0
            && count($this->appointments) === 0
            && count($this->teacherBirthdays) === 0
            && count($this->studentBirthdays) === 0
            && count($this->parentsDayAppointments) === 0;
    }
}
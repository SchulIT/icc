<?php

namespace App\Dashboard;

use App\Entity\Absence;
use App\Entity\Appointment;
use App\Entity\Exam;
use App\Entity\Infotext;
use App\Entity\Message;
use App\Entity\MessagePriority;
use App\Entity\Substitution;
use DateTime;

class DashboardView {

    /** @var DateTime */
    private $dateTime;

    /** @var Message[] */
    private $messages = [ ];

    /** @var Infotext[] */
    private $infotexts = [ ];

    /** @var Absence[] */
    private $absentTeachers = [ ];

    /** @var Absence[] */
    private $absentStudyGroups = [ ];

    /** @var DashboardLesson[] */
    private $lessons = [ ];

    /** @var DashboardLesson[] */
    private $beforeLessons = [ ];

    /** @var Message[] */
    private $priorityMessages = [ ];

    /** @var SubstitutionViewItem[] */
    private $substitutionMentions = [ ];

    /** @var ExamViewItem[] */
    private $exams = [ ];

    /** @var Appointment[] */
    private $appointments = [ ];

    public function __construct(DateTime $dateTime) {
        $this->dateTime = $dateTime;
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
        $lessons = array_merge(array_keys($this->lessons), array_keys($this->beforeLessons));
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
        if($message->getPriority()->equals(MessagePriority::Normal())) {
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

    public function isEmpty(): bool {
        return count($this->messages) === 0
            && count($this->infotexts) === 0
            && count($this->lessons) === 0
            && count($this->beforeLessons) === 0
            && count($this->absentStudyGroups) === 0
            && count($this->absentTeachers) === 0
            && count($this->priorityMessages) === 0
            && count($this->exams) === 0
            && count($this->appointments) === 0;
    }
}
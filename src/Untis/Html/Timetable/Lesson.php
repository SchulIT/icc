<?php

namespace App\Untis\Html\Timetable;

class Lesson {

    private array $weeks = [ ];
    private int $lessonStart;
    private int $lessonEnd;
    private int $day;
    private string $subject;
    private string $teacher;
    private ?string $room = null;
    private ?string $grade = null;

    /**
     * @return string[]
     */
    public function getWeeks(): array {
        return $this->weeks;
    }

    public function setWeeks(array $weeks): Lesson {
        $this->weeks = $weeks;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): Lesson {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): Lesson {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getDay(): int {
        return $this->day;
    }

    public function setDay(int $day): Lesson {
        $this->day = $day;
        return $this;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): Lesson {
        $this->subject = $subject;
        return $this;
    }

    public function getTeacher(): string {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): Lesson {
        $this->teacher = $teacher;
        return $this;
    }

    public function getRoom(): ?string {
        return $this->room;
    }

    public function setRoom(?string $room): Lesson {
        $this->room = $room;
        return $this;
    }

    public function getGrade(): ?string {
        return $this->grade;
    }

    public function setGrade(?string $grade): Lesson {
        $this->grade = $grade;
        return $this;
    }
}
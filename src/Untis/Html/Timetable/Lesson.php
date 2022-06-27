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

    /**
     * @param array $weeks
     * @return Lesson
     */
    public function setWeeks(array $weeks): Lesson {
        $this->weeks = $weeks;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return Lesson
     */
    public function setLessonStart(int $lessonStart): Lesson {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return Lesson
     */
    public function setLessonEnd(int $lessonEnd): Lesson {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @param int $day
     * @return Lesson
     */
    public function setDay(int $day): Lesson {
        $this->day = $day;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Lesson
     */
    public function setSubject(string $subject): Lesson {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getTeacher(): string {
        return $this->teacher;
    }

    /**
     * @param string $teacher
     * @return Lesson
     */
    public function setTeacher(string $teacher): Lesson {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string {
        return $this->room;
    }

    /**
     * @param string|null $room
     * @return Lesson
     */
    public function setRoom(?string $room): Lesson {
        $this->room = $room;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @param string|null $grade
     * @return Lesson
     */
    public function setGrade(?string $grade): Lesson {
        $this->grade = $grade;
        return $this;
    }
}
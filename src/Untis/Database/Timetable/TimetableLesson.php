<?php

namespace App\Untis\Database\Timetable;

class TimetableLesson {

    private string $teacher;
    private int $day;
    private int $lesson;
    private string $subject;
    private ?string $room;
    private ?int $tuitionNumber;
    private ?string $grade;
    private array $weeks;

    /**
     * @return string
     */
    public function getTeacher(): string {
        return $this->teacher;
    }

    /**
     * @param string $teacher
     * @return TimetableLesson
     */
    public function setTeacher(string $teacher): TimetableLesson {
        $this->teacher = $teacher;
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
     * @return TimetableLesson
     */
    public function setDay(int $day): TimetableLesson {
        $this->day = $day;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return TimetableLesson
     */
    public function setLesson(int $lesson): TimetableLesson {
        $this->lesson = $lesson;
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
     * @return TimetableLesson
     */
    public function setSubject(string $subject): TimetableLesson {
        $this->subject = $subject;
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
     * @return TimetableLesson
     */
    public function setRoom(?string $room): TimetableLesson {
        $this->room = $room;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTuitionNumber(): ?int {
        return $this->tuitionNumber;
    }

    /**
     * @param int|null $tuitionNumber
     * @return TimetableLesson
     */
    public function setTuitionNumber(?int $tuitionNumber): TimetableLesson {
        $this->tuitionNumber = $tuitionNumber;
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
     * @return TimetableLesson
     */
    public function setGrade(?string $grade): TimetableLesson {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return array
     */
    public function getWeeks(): array {
        return $this->weeks;
    }

    /**
     * @param array $weeks
     * @return TimetableLesson
     */
    public function setWeeks(array $weeks): TimetableLesson {
        $this->weeks = $weeks;
        return $this;
    }
}
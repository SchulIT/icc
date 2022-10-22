<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Lesson {

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("is_missing")
     */
    private bool $isMissing = true;

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("was_cancelled")
     */
    private bool $wasCancelled = false;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("start")
     */
    private int $start = 0;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("end")
     */
    private int $end = 0;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("subject")
     */
    private ?string $subject = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("replacement_subject")
     */
    private ?string $replacementSubject = null;

    /**
     * @Serializer\Type("App\Book\Export\Teacher")
     * @Serializer\SerializedName("teacher")
     */
    private ?Teacher $teacher = null;

    /**
     * @Serializer\Type("App\Book\Export\Teacher")
     * @Serializer\SerializedName("replacement_teacher")
     */
    private ?Teacher $replacementTeacher = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("topic")
     */
    private ?string $topic = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("comment")
     */
    private ?string $comment = null;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("exercises")
     */
    private ?string $exercises = null;

    /**
     * @Serializer\Type("array<App\Book\Export\Attendance>")
     * @Serializer\SerializedName("attendances")
     * @var Attendance[]
     */
    private array $attendances = [ ];

    public function isMissing(): bool {
        return $this->isMissing;
    }

    public function setIsMissing(bool $isMissing): Lesson {
        $this->isMissing = $isMissing;
        return $this;
    }

    public function isWasCancelled(): bool {
        return $this->wasCancelled;
    }

    public function setWasCancelled(bool $wasCancelled): Lesson {
        $this->wasCancelled = $wasCancelled;
        return $this;
    }

    public function getStart(): int {
        return $this->start;
    }

    public function setStart(int $start): Lesson {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): int {
        return $this->end;
    }

    public function setEnd(int $end): Lesson {
        $this->end = $end;
        return $this;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): Lesson {
        $this->subject = $subject;
        return $this;
    }

    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    public function setReplacementSubject(?string $replacementSubject): Lesson {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): Lesson {
        $this->teacher = $teacher;
        return $this;
    }

    public function getReplacementTeacher(): ?Teacher {
        return $this->replacementTeacher;
    }

    public function setReplacementTeacher(?Teacher $replacementTeacher): Lesson {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    public function getTopic(): ?string {
        return $this->topic;
    }

    public function setTopic(?string $topic): Lesson {
        $this->topic = $topic;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): Lesson {
        $this->comment = $comment;
        return $this;
    }

    public function getExercises(): ?string {
        return $this->exercises;
    }

    public function setExercises(?string $exercises): Lesson {
        $this->exercises = $exercises;
        return $this;
    }

    public function addAttendance(Attendance $attendance): void {
        $this->attendances[] = $attendance;
    }

    /**
     * @return Attendance[]
     */
    public function getAttendances(): array {
        return $this->attendances;
    }
}
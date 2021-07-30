<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Lesson {

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("is_missing")
     * @var bool
     */
    private $isMissing = true;

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("was_cancelled")
     * @var bool
     */
    private $wasCancelled = false;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("start")
     * @var int
     */
    private $start = 0;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("end")
     * @var int
     */
    private $end = 0;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("subject")
     * @var string
     */
    private $subject;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("replacement_subject")
     * @var string|null
     */
    private $replacementSubject;

    /**
     * @Serializer\Type("App\Book\Export\Teacher")
     * @Serializer\SerializedName("teacher")
     * @var Teacher
     */
    private $teacher;

    /**
     * @Serializer\Type("App\Book\Export\Teacher")
     * @Serializer\SerializedName("replacement_teacher")
     * @var Teacher|null
     */
    private $replacementTeacher;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("topic")
     * @var string|null
     */
    private $topic;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("comment")
     * @var string|null
     */
    private $comment;

    /**
     * @Serializer\Type("array<App\Book\Export\Attendance>")
     * @Serializer\SerializedName("attendances")
     * @var Attendance[]
     */
    private $attendances = [ ];

    /**
     * @return bool
     */
    public function isMissing(): bool {
        return $this->isMissing;
    }

    /**
     * @param bool $isMissing
     * @return Lesson
     */
    public function setIsMissing(bool $isMissing): Lesson {
        $this->isMissing = $isMissing;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWasCancelled(): bool {
        return $this->wasCancelled;
    }

    /**
     * @param bool $wasCancelled
     * @return Lesson
     */
    public function setWasCancelled(bool $wasCancelled): Lesson {
        $this->wasCancelled = $wasCancelled;
        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int {
        return $this->start;
    }

    /**
     * @param int $start
     * @return Lesson
     */
    public function setStart(int $start): Lesson {
        $this->start = $start;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int {
        return $this->end;
    }

    /**
     * @param int $end
     * @return Lesson
     */
    public function setEnd(int $end): Lesson {
        $this->end = $end;
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
     * @return string|null
     */
    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    /**
     * @param string|null $replacementSubject
     * @return Lesson
     */
    public function setReplacementSubject(?string $replacementSubject): Lesson {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return Lesson
     */
    public function setTeacher(Teacher $teacher): Lesson {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getReplacementTeacher(): ?Teacher {
        return $this->replacementTeacher;
    }

    /**
     * @param Teacher|null $replacementTeacher
     * @return Lesson
     */
    public function setReplacementTeacher(?Teacher $replacementTeacher): Lesson {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTopic(): ?string {
        return $this->topic;
    }

    /**
     * @param string|null $topic
     * @return Lesson
     */
    public function setTopic(?string $topic): Lesson {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return Lesson
     */
    public function setComment(?string $comment): Lesson {
        $this->comment = $comment;
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
<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Attendance {

    /**
     * @Serializer\Type("App\Book\Export\Student")
     * @Serializer\SerializedName("student")
     * @var Student
     */
    private $student;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("type")
     * @var string
     */
    private $type;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("absent_lesson_count")
     * @var int
     */
    private $absentLessonCount = 0;

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("is_excused")
     * @var bool
     */
    private $isExcused = false;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("comment")
     * @var string|null
     */
    private $comment;

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return Attendance
     */
    public function setStudent(Student $student): Attendance {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Attendance
     */
    public function setType(string $type): Attendance {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getAbsentLessonCount(): int {
        return $this->absentLessonCount;
    }

    /**
     * @param int $absentLessonCount
     * @return Attendance
     */
    public function setAbsentLessonCount(int $absentLessonCount): Attendance {
        $this->absentLessonCount = $absentLessonCount;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExcused(): bool {
        return $this->isExcused;
    }

    /**
     * @param bool $isExcused
     * @return Attendance
     */
    public function setIsExcused(bool $isExcused): Attendance {
        $this->isExcused = $isExcused;
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
     * @return Attendance
     */
    public function setComment(?string $comment): Attendance {
        $this->comment = $comment;
        return $this;
    }
}
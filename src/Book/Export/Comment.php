<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Comment {

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("date")
     * @var DateTime
     */
    private $date;

    /**
     * @Serializer\Type("App\Book\Export\Teacher")
     * @Serializer\SerializedName("teacher")
     * @var Teacher
     */
    private $teacher;

    /**
     * @Serializer\Type("array<App\Book\Export\Student>")
     * @Serializer\SerializedName("students")
     * @var Student[]
     */
    private $students;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("comment")
     * @var string
     */
    private $comment;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Comment
     */
    public function setDate(DateTime $date): Comment {
        $this->date = $date;
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
     * @return Comment
     */
    public function setTeacher(Teacher $teacher): Comment {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param Student[] $students
     * @return Comment
     */
    public function setStudents(array $students): Comment {
        $this->students = $students;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Comment
     */
    public function setComment(string $comment): Comment {
        $this->comment = $comment;
        return $this;
    }
}
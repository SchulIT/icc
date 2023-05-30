<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Comment {

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('date')]
    private ?DateTime $date = null;

    #[Serializer\Type(Teacher::class)]
    #[Serializer\SerializedName('teacher')]
    private ?Teacher $teacher = null;

    /**
     * @var Student[]
     */
    #[Serializer\Type('array<App\Book\Export\Student>')]
    #[Serializer\SerializedName('students')]
    private ?array $students = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('comment')]
    private ?string $comment = null;

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): Comment {
        $this->date = $date;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

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
     */
    public function setStudents(array $students): Comment {
        $this->students = $students;
        return $this;
    }

    public function getComment(): string {
        return $this->comment;
    }

    public function setComment(string $comment): Comment {
        $this->comment = $comment;
        return $this;
    }
}
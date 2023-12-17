<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamTuition {
    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $subjectOrCourse = null;

    /**
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    #[Serializer\Type('array<string>')]
    private array $grades = [ ];

    /**
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    #[Serializer\Type('array<string>')]
    private array $teachers = [ ];

    /**
     * ID der SuS, die an der Klausur dieses Unterrichtes teilnehmen.
     *
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
    private array $students = [ ];

    public function getSubjectOrCourse(): ?string {
        return $this->subjectOrCourse;
    }

    public function setSubjectOrCourse(?string $subjectOrCourse): ExamTuition {
        $this->subjectOrCourse = $subjectOrCourse;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param string[] $grades
     */
    public function setGrades(array $grades): ExamTuition {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param string[] $teachers
     */
    public function setTeachers(array $teachers): ExamTuition {
        $this->teachers = $teachers;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param string[] $students
     */
    public function setStudents(array $students): void {
        $this->students = $students;
    }
}
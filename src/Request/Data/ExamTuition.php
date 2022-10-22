<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamTuition {
    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $subjectOrCourse = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    private array $grades = [ ];

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    private array $teachers = [ ];

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
}
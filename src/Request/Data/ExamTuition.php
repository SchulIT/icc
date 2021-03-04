<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamTuition {
    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $subjectOrCourse;

    /**
     * @Serializer\Type("array<string>")
     * @Assert\Count(min="1")
     * @var string[]
     */
    private $grades = [ ];

    /**
     * @Serializer\Type("array<string>")
     * @Assert\Count(min="1")
     * @var string[]
     */
    private $teachers = [ ];

    /**
     * @return string|null
     */
    public function getSubjectOrCourse(): ?string {
        return $this->subjectOrCourse;
    }

    /**
     * @param string|null $subjectOrCourse
     * @return ExamTuition
     */
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
     * @return ExamTuition
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
     * @return ExamTuition
     */
    public function setTeachers(array $teachers): ExamTuition {
        $this->teachers = $teachers;
        return $this;
    }
}
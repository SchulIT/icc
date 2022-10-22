<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentsData {

    /**
     * @Serializer\Type("int")
     */
    private ?int $year = null;

    /**
     * @Serializer\Type("int")
     */
    private ?int $section = null;

    /**
     * @Serializer\Type("array<App\Request\Data\StudentData>")
     * @UniqueId(propertyPath="id")
     * @var StudentData[]
     */
    #[Assert\Valid]
    private array $students = [ ];

    /**
     * @return StudentData[]
     */
    public function getStudents() {
        return $this->students;
    }

    /**
     * @param StudentData[] $students
     */
    public function setStudents($students): StudentsData {
        $this->students = $students;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): StudentsData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): StudentsData {
        $this->section = $section;
        return $this;
    }
}
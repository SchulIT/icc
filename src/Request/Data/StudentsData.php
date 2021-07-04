<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentsData {

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $year;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $section;

    /**
     * @Serializer\Type("array<App\Request\Data\StudentData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var StudentData[]
     */
    private $students = [ ];

    /**
     * @return StudentData[]
     */
    public function getStudents() {
        return $this->students;
    }

    /**
     * @param StudentData[] $students
     * @return StudentsData
     */
    public function setStudents($students): StudentsData {
        $this->students = $students;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @param int $year
     * @return StudentsData
     */
    public function setYear(int $year): StudentsData {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int
     */
    public function getSection(): int {
        return $this->section;
    }

    /**
     * @param int $section
     * @return StudentsData
     */
    public function setSection(int $section): StudentsData {
        $this->section = $section;
        return $this;
    }
}
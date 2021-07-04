<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeachersData {

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
     * @Serializer\Type("array<App\Request\Data\TeacherData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var TeacherData[]
     */
    private $teachers = [ ];

    /**
     * @return TeacherData[]
     */
    public function getTeachers() {
        return $this->teachers;
    }

    /**
     * @param TeacherData[] $teachers
     * @return TeachersData
     */
    public function setTeachers($teachers): TeachersData {
        $this->teachers = $teachers;
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
     * @return TeachersData
     */
    public function setYear(int $year): TeachersData {
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
     * @return TeachersData
     */
    public function setSection(int $section): TeachersData {
        $this->section = $section;
        return $this;
    }
}
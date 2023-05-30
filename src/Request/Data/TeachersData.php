<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeachersData {

    #[Serializer\Type('int')]
    private ?int $year = null;

    #[Serializer\Type('int')]
    private ?int $section = null;

    /**
     * @var TeacherData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\TeacherData>')]
    private array $teachers = [ ];

    /**
     * @return TeacherData[]
     */
    public function getTeachers() {
        return $this->teachers;
    }

    /**
     * @param TeacherData[] $teachers
     */
    public function setTeachers($teachers): TeachersData {
        $this->teachers = $teachers;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): TeachersData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): TeachersData {
        $this->section = $section;
        return $this;
    }
}
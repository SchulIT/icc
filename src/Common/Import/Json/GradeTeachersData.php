<?php

namespace App\Common\Import\Json;

use App\Common\Import\Json\GradeTeacherData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeachersData {

    #[Serializer\Type('int')]
    private ?int $year = null;

    #[Serializer\Type('int')]
    private ?int $section = null;

    /**
     * @var GradeTeacherData[]
     */
    #[Assert\Valid]
    #[Serializer\Type('array<' . GradeTeacherData::class . '>')]
    private array $gradeTeachers = [ ];

    /**
     * @return GradeTeacherData[]
     */
    public function getGradeTeachers() {
        return $this->gradeTeachers;
    }

    /**
     * @param GradeTeacherData[] $gradeTeachers
     */
    public function setGradeTeachers($gradeTeachers): GradeTeachersData {
        $this->gradeTeachers = $gradeTeachers;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): GradeTeachersData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): GradeTeachersData {
        $this->section = $section;
        return $this;
    }
}
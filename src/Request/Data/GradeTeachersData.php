<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeachersData {

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
     * @Serializer\Type("array<App\Request\Data\GradeTeacherData>")
     * @Assert\Valid()
     * @var GradeTeacherData[]
     */
    private $gradeTeachers = [ ];

    /**
     * @return GradeTeacherData[]
     */
    public function getGradeTeachers() {
        return $this->gradeTeachers;
    }

    /**
     * @param GradeTeacherData[] $gradeTeachers
     * @return GradeTeachersData
     */
    public function setGradeTeachers($gradeTeachers): GradeTeachersData {
        $this->gradeTeachers = $gradeTeachers;
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
     * @return GradeTeachersData
     */
    public function setYear(int $year): GradeTeachersData {
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
     * @return GradeTeachersData
     */
    public function setSection(int $section): GradeTeachersData {
        $this->section = $section;
        return $this;
    }
}
<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeachersData {

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

}
<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeachersData {

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
}
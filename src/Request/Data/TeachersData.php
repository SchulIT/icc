<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeachersData {

    /**
     * @Serializer\Type("array<App\Request\Data\TeacherData>")
     * @Assert\Valid()
     * @var TeacherData[]
     */
    private $teachers;

    /**
     * @return TeacherData[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param TeacherData[] $teachers
     * @return TeachersData
     */
    public function setTeachers(array $teachers): TeachersData {
        $this->teachers = $teachers;
        return $this;
    }
}
<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentsData {

    /**
     * @Serializer\Type("array<App\Request\Data\StudentData>")
     * @Assert\Valid()
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
}
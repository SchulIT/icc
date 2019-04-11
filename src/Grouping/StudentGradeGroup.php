<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Student;

class StudentGradeGroup implements GroupInterface {
    /**
     * @var Grade
     */
    private $grade;

    /**
     * @var Student[]
     */
    private $students = [ ];

    public function __construct(Grade $grade) {
        $this->grade = $grade;
    }

    /**
     * @return Grade
     */
    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @return Student[]
     */
    public function &getStudents(): array {
        return $this->students;
    }

    /**
     * @return Grade
     */
    public function getKey() {
        return $this->grade;
    }

    /**
     * @param Student $item
     */
    public function addItem($item) {
        $this->students[] = $item;
    }


}
<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Student;

class StudentGradeGroup implements GroupInterface, SortableGroupInterface {
    /**
     * @var Grade
     */
    private $grade;

    /**
     * @var Student[]
     */
    private $students = [ ];

    public function __construct(?Grade $grade) {
        $this->grade = $grade;
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @return Student[]
     */
    public function &getStudents(): array {
        return $this->students;
    }

    /**
     * @return Grade|null
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


    public function &getItems(): array {
        return $this->students;
    }
}
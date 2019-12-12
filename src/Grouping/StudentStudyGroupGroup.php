<?php

namespace App\Grouping;

use App\Entity\Student;
use App\Entity\StudyGroup;

class StudentStudyGroupGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudyGroup */
    private $studyGroup;

    /** @var Student[] */
    private $students = [ ];

    public function __construct(StudyGroup $studyGroup) {
        $this->studyGroup = $studyGroup;
    }

    public function getStudyGroup() {
        return $this->studyGroup;
    }

    public function getStudents() {
        return $this->students;
    }

    public function getKey() {
        return $this->studyGroup;
    }

    public function addItem($item) {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }
}
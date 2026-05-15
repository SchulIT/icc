<?php

namespace App\Common\Grouping;

use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

class StudentStudyGroupGroup implements GroupInterface, SortableGroupInterface {

    /** @var Student[] */
    private array $students = [ ];

    public function __construct(private StudyGroup $studyGroup)
    {
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
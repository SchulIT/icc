<?php

namespace App\Grouping;

use App\Entity\StudentAbsence;
use App\Entity\Student;

class StudentAbsenceStudentGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct(private Student $student)
    {
    }

    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey() {
        return $this->getStudent();
    }

    public function addItem($item) {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
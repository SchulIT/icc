<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\StudentAbsence;

class StudentAbsenceGradeGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct(private Grade $grade)
    {
    }

    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey() {
        return $this->grade;
    }

    public function addItem($item) {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
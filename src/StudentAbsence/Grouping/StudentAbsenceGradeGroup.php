<?php

namespace App\StudentAbsence\Grouping;

use App\Common\Entity\Grade;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\StudentAbsence\Entity\StudentAbsence;

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
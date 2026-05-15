<?php

namespace App\StudentAbsence\Grouping;

use App\Common\Entity\Grade;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\StudentAbsence\Entity\StudentAbsence;

/**
 * @implements SortableGroupInterface<Grade, StudentAbsence>
 */
class StudentAbsenceGradeGroup implements SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct(private readonly Grade $grade)
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

    public function getKey(): Grade {
        return $this->grade;
    }

    public function addItem($item): void {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
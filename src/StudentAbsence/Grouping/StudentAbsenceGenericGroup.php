<?php

namespace App\StudentAbsence\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\StudentAbsence\Entity\StudentAbsence;

/**
 * @implements SortableGroupInterface<null, StudentAbsence>
 */
class StudentAbsenceGenericGroup implements SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct() {    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey(): null {
        return null;
    }

    public function addItem($item): void {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
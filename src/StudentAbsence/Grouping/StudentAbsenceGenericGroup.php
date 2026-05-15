<?php

namespace App\StudentAbsence\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\StudentAbsence\Entity\StudentAbsence;

class StudentAbsenceGenericGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct() {    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey() {
        return null;
    }

    public function addItem($item) {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
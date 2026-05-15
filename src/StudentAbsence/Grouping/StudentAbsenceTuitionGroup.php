<?php

namespace App\StudentAbsence\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\Tuition;

/**
 * @implements SortableGroupInterface<Tuition, StudentAbsence>
 */
class StudentAbsenceTuitionGroup implements SortableGroupInterface {

    /** @var StudentAbsence[] */
    private array $absences = [ ];

    public function __construct(private readonly Tuition $tuition)
    {
    }

    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @return StudentAbsence[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    public function getKey(): mixed {
        return $this->tuition;
    }

    public function addItem($item): void {
        $this->absences[] = $item;
    }

    public function &getItems(): array {
        return $this->absences;
    }
}
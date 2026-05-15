<?php

namespace App\StudentAbsence\View\Filter;

use App\StudentAbsence\Entity\StudentAbsenceType;

class StudentAbsenceTypeFilterView {

    /**
     * @param StudentAbsenceType[] $types
     */
    public function __construct(private array $types, private ?StudentAbsenceType $currentType)
    {
    }

    /**
     * @return StudentAbsenceType[]
     */
    public function getTypes(): array {
        return $this->types;
    }

    public function getCurrentType(): ?StudentAbsenceType {
        return $this->currentType;
    }
}
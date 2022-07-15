<?php

namespace App\View\Filter;

use App\Entity\StudentAbsenceType;

class StudentAbsenceTypeFilterView {

    /** @var StudentAbsenceType[] */
    private array $types;

    private ?StudentAbsenceType $currentType;

    public function __construct(array $types, ?StudentAbsenceType $currentType) {
        $this->types = $types;
        $this->currentType = $currentType;
    }

    /**
     * @return StudentAbsenceType[]
     */
    public function getTypes(): array {
        return $this->types;
    }

    /**
     * @return StudentAbsenceType|null
     */
    public function getCurrentType(): ?StudentAbsenceType {
        return $this->currentType;
    }
}
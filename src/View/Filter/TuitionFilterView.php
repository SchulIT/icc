<?php

namespace App\View\Filter;

use App\Entity\Tuition;

class TuitionFilterView {

    /**
     * @param Tuition[] $tuitions
     */
    public function __construct(private array $tuitions, private ?Tuition $currentTuition)
    {
    }

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    public function getCurrentTuition(): ?Tuition {
        return $this->currentTuition;
    }
}
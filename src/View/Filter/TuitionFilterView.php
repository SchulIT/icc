<?php

namespace App\View\Filter;

use App\Entity\Tuition;

class TuitionFilterView {

    /** @var Tuition[] */
    private $tuitions;

    /** @var Tuition|null */
    private $currentTuition;

    public function __construct(array $tuitions, ?Tuition $currentTuition) {
        $this->tuitions = $tuitions;
        $this->currentTuition = $currentTuition;
    }

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @return Tuition|null
     */
    public function getCurrentTuition(): ?Tuition {
        return $this->currentTuition;
    }
}
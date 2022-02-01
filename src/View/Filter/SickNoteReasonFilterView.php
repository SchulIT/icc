<?php

namespace App\View\Filter;

use App\Entity\SickNoteReason;

class SickNoteReasonFilterView {
    /** @var SickNoteReason[] */
    private array $reasons;

    private ?SickNoteReason $currentReason;

    public function __construct(array $reasons, ?SickNoteReason $currentReason) {
        $this->reasons = $reasons;
        $this->currentReason = $currentReason;
    }

    /**
     * @return SickNoteReason[]
     */
    public function getReasons(): array {
        return $this->reasons;
    }

    /**
     * @return SickNoteReason|null
     */
    public function getCurrentReason(): ?SickNoteReason {
        return $this->currentReason;
    }
}
<?php

namespace App\View\Filter;

use App\Entity\SickNoteReason;
use UnexpectedValueException;

class SickNoteReasonFilter {
    public function handle(?string $reason): SickNoteReasonFilterView {
        $reasons = SickNoteReason::values();
        $currentReason = null;
        try {
            $currentReason = SickNoteReason::from($reason);
        } catch (UnexpectedValueException $e) { }

        return new SickNoteReasonFilterView($reasons, $currentReason);
    }
}
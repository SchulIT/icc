<?php

namespace App\Tools\WestermannZvs\Check;

use App\Tools\WestermannZvs\StudentMatch;
use Override;

readonly class IsWestermannConsentedCheck implements CheckInterface {

    #[Override]
    public function needAction(StudentMatch $match): Action|null {
        if($match->schueler === null || $match->student === null || $match->isConsented) {
            return null;
        }

        return new Action('check.westermann_zsv.consent_missing');
    }
}
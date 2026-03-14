<?php

namespace App\Tools\WestermannZvs\Check;

use App\Tools\WestermannZvs\StudentMatch;
use Override;

readonly class IsWestermannAccountExistentCheck implements CheckInterface {
    #[Override]
    public function needAction(StudentMatch $match): Action|null {
        if($match->schueler === null && $match->student !== null && $match->isConsented === true) {
            return new Action('check.westermann_zsv.account_missing');
        }

        return null;
    }
}
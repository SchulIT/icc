<?php

namespace App\Tools\WestermannZvs\Check;

use App\Tools\WestermannZvs\StudentMatch;
use Override;

class IsPasswortSetCheck implements CheckInterface {

    #[Override]
    public function needAction(StudentMatch $match): Action|null {
        if($match->isConsented === false || $match->student === null) {
            return null;
        }

        if($match->isPasswordSet) {
            return null;
        }

        return new Action('check.westermann_zsv.password_missing');
    }
}
<?php

namespace App\Tools\WestermannZvs\Check;

use App\Tools\WestermannZvs\StudentMatch;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(CheckInterface::AUTOCONFIGURE_KEY)]
interface CheckInterface {

    public const string AUTOCONFIGURE_KEY = 'tools.westermann_zsv.check';

    public function needAction(StudentMatch $match): Action|null;
}
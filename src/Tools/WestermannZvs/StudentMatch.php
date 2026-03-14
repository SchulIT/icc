<?php

namespace App\Tools\WestermannZvs;

use App\Entity\Student;
use App\Entity\StudentLearningManagementSystemInformation;

class StudentMatch {
    public string $username;

    public ?Student $student = null;

    public ?Schueler $schueler = null;

    public bool $isConsented = false;

    public array $actions = [ ];

    public bool $needsAction = false;
}
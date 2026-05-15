<?php

namespace App\Tools\WestermannZvs;

use App\Common\Entity\Student;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;

class StudentMatch {
    public string $username;

    public ?Student $student = null;

    public ?Schueler $schueler = null;

    public bool $isConsented = false;

    public bool $isPasswordSet = false;

    public array $actions = [ ];

    public bool $needsAction = false;
}
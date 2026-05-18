<?php

namespace App\Book\AttendanceSuggestion;

use App\Common\Entity\Student;
use App\Book\Xhr\Response\AttendanceSuggestion;

class PrioritizedSuggestion {
    public function __construct(private readonly int $priority, private readonly Student $student, private readonly AttendanceSuggestion $suggestion) { }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getSuggestion(): AttendanceSuggestion {
        return $this->suggestion;
    }
}
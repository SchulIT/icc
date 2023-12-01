<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Student;
use App\Response\Book\AttendanceSuggestion;

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
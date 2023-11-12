<?php

namespace App\Messenger;

use DateTime;

class RunIntegrityCheckMessage {
    public function __construct(private readonly int $studentId, private readonly DateTime $start, private readonly DateTime $end) { }

    public function getStudentId(): int {
        return $this->studentId;
    }

    public function getStart(): DateTime {
        return $this->start;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }
}
<?php

namespace App\Messenger;

use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class RunIntegrityCheckMessage {
    public function __construct(private int $studentId, private DateTime $start, private DateTime $end) { }

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
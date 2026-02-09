<?php

namespace App\Book\Excuse;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class AssociateAttendanceMessage {
    public function __construct(
        public int $attendanceId
    ) {

    }
}
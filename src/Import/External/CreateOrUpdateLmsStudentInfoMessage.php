<?php

namespace App\Import\External;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class CreateOrUpdateLmsStudentInfoMessage {
    public function __construct(
        public int $studentId,
        public int $lmsId,
        public string $username,
        public string $password,
    ) {

    }
}
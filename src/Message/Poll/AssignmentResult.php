<?php

namespace App\Message\Poll;

readonly class AssignmentResult {
    public function __construct(
        public array $assigned,
        public array $notAssigned,
        public string|null $output = null
    ) { }
}
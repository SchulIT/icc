<?php

namespace App\Book\Student\Cache;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class GenerateStudentInfoCountsMessage {
    public function __construct(public int $studentId, public int $sectionId, public ContextType $contextType, public int $contextId) { }
}
<?php

namespace App\Book\Statistics;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class GenerateBookLessonCountMessage {
    public function __construct(private int $tuitionId) { }

    /**
     * @return int
     */
    public function getTuitionId(): int {
        return $this->tuitionId;
    }
}
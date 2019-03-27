<?php

namespace App\Event;

use App\Entity\TimetableLesson;
use Symfony\Component\EventDispatcher\Event;

class TimetableImportEvent extends Event {
    /** @var TimetableLesson[] */
    private $lessons = [ ];

    /**
     * TimetableImportEvent constructor.
     * @param TimetableLesson[] $lessons
     */
    public function __construct(array $lessons = [ ]) {
        $this->lessons = $lessons;
    }

    /**
     * @return TimetableLesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }
}
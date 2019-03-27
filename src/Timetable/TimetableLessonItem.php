<?php

namespace App\Timetable;

use App\Entity\Tuition;

class TimetableLessonItem {

    /** @var Tuition */
    private $tuition;

    /** @var string */
    private $room;

    public function __construct(Tuition $tuition, string $room) {
        $this->tuition = $tuition;
        $this->room = $room;
    }

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @return string
     */
    public function getRoom(): string {
        return $this->room;
    }
}
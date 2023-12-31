<?php

namespace App\Messenger;

use DateTime;

class ResolveTimetableLessonsForAbsenceLessonMessage {

    public function __construct(private readonly DateTime $startDate, private readonly DateTime $endDate) {    }

    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    public function getEndDate(): DateTime {
        return $this->endDate;
    }
}
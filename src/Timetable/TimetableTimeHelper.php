<?php

namespace App\Timetable;

use App\Settings\TimetableSettings;
use DateInterval;
use DateTime;
use Exception;

class TimetableTimeHelper {

    private $timetableSettings;

    public function __construct(TimetableSettings $timetableSettings) {
        $this->timetableSettings = $timetableSettings;
    }

    private function getDateTime(DateTime $date, ?string $time) {
        if(empty($time) || strpos($time, ':') === false) {
            return $date;
        }

        list($hour, $minute) = explode(':', $time);

        try {
            $interval = new DateInterval(sprintf('PT%dH%dM', $hour, $minute));
            return (clone $date)->add($interval);
        } catch (Exception $e) {
            return $date;
        }
    }

    public function getLessonStartDateTime(DateTime $date, int $lesson, bool $isBefore = false): DateTime {
        if($isBefore === true) {
            return $this->getDateTime($date, $this->timetableSettings->getEnd($lesson - 1));
        }

        return $this->getDateTime($date, $this->timetableSettings->getStart($lesson));
    }

    public function getLessonEndDateTime(DateTime $date, int $lesson, bool $isBefore = false): DateTime {
        if($isBefore === true) {
            return $this->getDateTime($date, $this->timetableSettings->getStart($lesson));
        }

        return $this->getDateTime($date, $this->timetableSettings->getEnd($lesson));
    }
}
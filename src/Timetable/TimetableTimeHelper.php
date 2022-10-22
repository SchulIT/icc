<?php

namespace App\Timetable;

use App\Entity\DateLesson;
use App\Settings\TimetableSettings;
use DateInterval;
use DateTime;
use Exception;

class TimetableTimeHelper {

    public function __construct(private TimetableSettings $timetableSettings)
    {
    }

    private function getDateTime(DateTime $date, ?string $time) {
        if(empty($time) || !str_contains($time, ':')) {
            return $date;
        }

        [$hour, $minute] = explode(':', $time);

        try {
            $interval = new DateInterval(sprintf('PT%dH%dM', $hour, $minute));
            return (clone $date)->add($interval);
        } catch (Exception) {
            return $date;
        }
    }

    public function getLessonStartDateTime(DateTime $date, int $lesson, bool $isBefore = false): DateTime {
        if($isBefore === true) {
            if($lesson === 1) {
                return $this->getDateTime($date, $this->timetableSettings->getStart(0));
            }

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

    public function getLessonDateForDateTime(DateTime $dateTime): DateLesson {
        $dateLesson = new DateLesson();
        $dateLesson->setLesson(1);
        $date = clone $dateTime;
        $date->setTime(0, 0, 0);
        $dateLesson->setDate($date);

        for($lesson = $this->timetableSettings->getMaxLessons(); $lesson >= 1; $lesson--) {
            if($this->getLessonStartDateTime($date, $lesson, false) > $dateTime) {
                $dateLesson->setLesson($lesson);
            }
        }

        return $dateLesson;
    }
}
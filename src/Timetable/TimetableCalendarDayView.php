<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;
use DateTime;

class TimetableCalendarDayView {

    /** @var DateTime  */
    private $day;

    /** @var TimetableLessonEntity[] */
    private $lessons;

    /** @var TimetableSupervision[] */
    private $supervisions;

    public function __construct(DateTime $day, array $lessons, array $supervisions) {
        $this->day = $day;
        $this->lessons = $lessons;
        $this->supervisions = $supervisions;
    }

    /**
     * @return DateTime
     */
    public function getDay(): DateTime {
        return $this->day;
    }

    /**
     * @return TimetableLessonEntity[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }
}
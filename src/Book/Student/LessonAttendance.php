<?php

namespace App\Book\Student;

use App\Entity\LessonAttendance as LessonAttendanceEntity;
use DateTime;

class LessonAttendance {

    /** @var DateTime  */
    private $date;

    /** @var int  */
    private $lesson;

    /** @var LessonAttendanceEntity  */
    private $attendance;

    /** @var ExcuseCollection  */
    private $excuses;

    public function __construct(DateTime $date, int $lesson, LessonAttendanceEntity $attendance, ExcuseCollection $excuses) {
        $this->date = $date;
        $this->lesson = $lesson;
        $this->attendance = $attendance;
        $this->excuses = $excuses;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @return LessonAttendanceEntity
     */
    public function getAttendance(): LessonAttendanceEntity {
        return $this->attendance;
    }

    /**
     * @return ExcuseCollection
     */
    public function getExcuses(): ExcuseCollection {
        return $this->excuses;
    }
}
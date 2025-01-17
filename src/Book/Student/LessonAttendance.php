<?php

namespace App\Book\Student;

use App\Entity\Attendance as LessonAttendanceEntity;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceFlag;
use DateTime;
use JsonSerializable;

class LessonAttendance implements JsonSerializable {

    public function __construct(private DateTime $date, private int $lesson, private LessonAttendanceEntity $attendance, private ExcuseCollection $excuses)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function getAttendance(): LessonAttendanceEntity {
        return $this->attendance;
    }

    public function isExcused(): bool {
        if($this->getExcuses()->count() > 0) {
            return true;
        }

        if($this->getAttendance()->getExcuseStatus() === AttendanceExcuseStatus::NotExcused) {
            return false;
        }

        if($this->getAttendance()->getExcuseStatus() === AttendanceExcuseStatus::Excused) {
            return true;
        }

        return false;
    }

    public function getExcuses(): ExcuseCollection {
        return $this->excuses;
    }

    public function jsonSerialize(): array {
        return [
            'date' => $this->date->format('c'),
            'lesson' => $this->getLesson(),
            'has_excuses' => count($this->excuses) > 0,
            'entry' => $this->attendance->getEntry()?->getUuid()->toString(),
            'event' => $this->attendance->getEvent()?->getUuid()->toString(),
            'attendance' => [
                'uuid' => $this->attendance->getUuid()->toString(),
                'type' => $this->attendance->getType(),
                'late_minutes' => $this->attendance->getLateMinutes(),
                'zero_absent_lesson' => $this->attendance->isZeroAbsentLesson(),
                'comment' => $this->attendance->getComment(),
                'excuse_status' => $this->attendance->getExcuseStatus(),
                'flags' => $this->attendance->getFlags()->map(fn(AttendanceFlag $flag) => $flag->jsonSerialize())->toArray()
            ]
        ];
    }
}
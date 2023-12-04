<?php

namespace App\Book\Student;

use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceFlag;
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

        if($this->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotExcused) {
            return false;
        }

        if($this->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::Excused) {
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
            'entry' => $this->attendance->getEntry()->getUuid()->toString(),
            'attendance' => [
                'uuid' => $this->attendance->getUuid()->toString(),
                'type' => $this->attendance->getType(),
                'late_minutes' => $this->attendance->getLateMinutes(),
                'absent_lessons' => $this->attendance->getAbsentLessons(),
                'comment' => $this->attendance->getComment(),
                'excuse_status' => $this->attendance->getExcuseStatus(),
                'flags' => $this->attendance->getFlags()->map(fn(LessonAttendanceFlag $flag) => $flag->jsonSerialize())->toArray()
            ]
        ];
    }
}
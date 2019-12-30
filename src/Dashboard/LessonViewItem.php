<?php

namespace App\Dashboard;

use App\Entity\TimetableLesson;

class LessonViewItem extends AbstractViewItem {

    private $isOutdated;
    private $lesson;
    private $absentStudents = [ ];

    /**
     * @param TimetableLesson $lesson
     * @param AbsentStudent[] $absentStudents
     * @param bool $isOutdated
     */
    public function __construct(TimetableLesson $lesson, array $absentStudents, bool $isOutdated) {
        $this->lesson = $lesson;
        $this->absentStudents = $absentStudents;
        $this->isOutdated = $isOutdated;
    }

    /**
     * @return bool
     */
    public function isOutdated(): bool {
        return $this->isOutdated;
    }

    /**
     * @return TimetableLesson
     */
    public function getLesson(): TimetableLesson {
        return $this->lesson;
    }

    /**
     * @return AbsentStudent[]
     */
    public function getAbsentStudents(): array {
        return $this->absentStudents;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}
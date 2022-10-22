<?php

namespace App\Dashboard;

use App\Entity\TimetableLesson;
use App\Grouping\AbsentStudentGroup;

class TimetableLessonViewItem extends AbsenceAwareViewItem {

    /** @var TimetableLesson[] */
    private array $additionalLessons = [ ];

    /**
     * @param TimetableLesson|null $lesson
     * @param AbsentStudentGroup[] $absentStudentGroups
     */
    public function __construct(private ?TimetableLesson $lesson, array $absentStudentGroups) {
        parent::__construct($absentStudentGroups);
    }

    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    public function addAdditionalLesson(TimetableLesson $timetableLesson): void {
        $this->additionalLessons[] = $timetableLesson;
    }

    /**
     * @return TimetableLesson[]
     */
    public function getAdditionalLessons(): array {
        return $this->additionalLessons;
    }

    public function isMerged(): bool {
        return count($this->additionalLessons) > 0;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}
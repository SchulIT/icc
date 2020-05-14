<?php

namespace App\Dashboard;

use App\Entity\TimetableLesson;
use App\Grouping\AbsentStudentGroup;

class LessonViewItem extends AbstractViewItem {

    private $isOutdated;
    private $lesson;
    private $absentStudentGroups = [ ];

    private $mergedItems = [ ];

    /**
     * @param TimetableLesson|null $lesson
     * @param AbsentStudentGroup[] $absentStudentGroups
     * @param bool $isOutdated
     */
    public function __construct(?TimetableLesson $lesson, array $absentStudentGroups, bool $isOutdated) {
        $this->lesson = $lesson;
        $this->absentStudentGroups = $absentStudentGroups;
        $this->isOutdated = $isOutdated;
    }

    /**
     * @return bool
     */
    public function isOutdated(): bool {
        return $this->isOutdated;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getMergedItems(): array {
        return $this->mergedItems;
    }

    /**
     * @param AbstractViewItem $item
     */
    public function addMergedItem(AbstractViewItem $item): void {
        $this->mergedItems[] = $item;
    }

    /**
     * @return AbsentStudent[]
     */
    public function getAbsentStudentGroups(): array {
        return $this->absentStudentGroups;
    }

    public function getAbsentStudentsCount(): int {
        $count = 0;

        foreach($this->absentStudentGroups as $group) {
            $count += count($group->getStudents());
        }

        return $count;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}
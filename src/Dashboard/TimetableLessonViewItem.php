<?php

namespace App\Dashboard;

use App\Entity\TimetableLesson;
use App\Grouping\AbsentStudentGroup;

class TimetableLessonViewItem extends AbstractViewItem {

    /** @var TimetableLesson|null */
    private $lesson;

    /** @var AbsentStudentGroup[] */
    private $absentStudentGroups = [ ];

    /**
     * @param TimetableLesson|null $lesson
     * @param AbsentStudentGroup[] $absentStudentGroups
     */
    public function __construct(?TimetableLesson $lesson, array $absentStudentGroups) {
        $this->lesson = $lesson;
        $this->absentStudentGroups = $absentStudentGroups;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
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
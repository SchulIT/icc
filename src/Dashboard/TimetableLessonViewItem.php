<?php

namespace App\Dashboard;

use App\Entity\TimetableLesson;
use App\Grouping\AbsentStudentGroup;

class TimetableLessonViewItem extends AbstractViewItem {

    /** @var TimetableLesson|null */
    private $lesson;

    /** @var TimetableLesson[] */
    private $additionalLessons = [ ];

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
     * @return AbsentStudentGroup[]
     */
    public function getAbsentStudentGroups(): array {
        return $this->absentStudentGroups;
    }

    public function getAbsentStudentsCount(): int {
        $count = 0;
        $studentIds = [ ];

        foreach($this->absentStudentGroups as $group) {
            foreach($group->getStudents() as $student) {
                if(!in_array($student->getStudent()->getId(), $studentIds)) {
                    $count++;
                    $studentIds[] = $student->getStudent()->getId();
                }
            }
        }

        return $count;
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
<?php

namespace App\Dashboard;

use App\Entity\StudentInformation;
use App\Entity\TeacherAbsenceComment;
use App\Entity\TimetableLesson;
use App\Entity\TimetableLessonAdditionalInformation;
use App\Grouping\AbsentStudentGroup;

class TimetableLessonViewItem extends AdditionalExtraAwareViewItem {

    /** @var TimetableLesson[] */
    private array $additionalLessons = [ ];

    /**
     * @param TimetableLesson|null $lesson
     * @param AbsentStudentGroup[] $absentStudentGroups
     * @param StudentInformation[] $studentInfo
     * @param TimetableLessonAdditionalInformation[] $additionalInformation
     */
    public function __construct(private readonly ?TimetableLesson $lesson, array $absentStudentGroups, array $studentInfo, private readonly array $additionalInformation) {
        parent::__construct($absentStudentGroups, $studentInfo);
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

    /**
     * @return TimetableLessonAdditionalInformation[]
     */
    public function getAdditionalInformation(): array {
        return $this->additionalInformation;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}
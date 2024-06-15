<?php

namespace App\Dashboard;

use App\Entity\BookStudentInformation;
use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;
use App\Grouping\AbsentStudentGroup;

class TimetableLessonViewItem extends AdditionalExtraAwareViewItem {

    /** @var TimetableLesson[] */
    private array $additionalLessons = [ ];

    /**
     * @param TimetableLesson|null $lesson
     * @param AbsentStudentGroup[] $absentStudentGroups
     * @param BookStudentInformation[] $studentInfo
     */
    public function __construct(private readonly ?TimetableLesson $lesson, array $absentStudentGroups, array $studentInfo, private readonly ?TeacherAbsenceLesson $absenceLesson) {
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
     * @return TeacherAbsenceLesson|null
     */
    public function getAbsenceLesson(): ?TeacherAbsenceLesson {
        return $this->absenceLesson;
    }

    public function getBlockName(): string {
        return 'lesson';
    }
}
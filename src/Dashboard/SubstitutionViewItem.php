<?php

namespace App\Dashboard;

use App\Entity\Student;
use App\Entity\Substitution;
use App\Entity\TeacherAbsenceComment;
use App\Entity\TimetableLesson;
use App\Entity\TimetableLessonAdditionalInformation;

class SubstitutionViewItem extends AdditionalExtraAwareViewItem {

    /**
     * @param Substitution $substitution
     * @param bool $isFreeLessonType
     * @param array $students
     * @param array $absentStudentGroups
     * @param array $studentInfo
     * @param TimetableLesson|null $lesson
     * @param TimetableLessonAdditionalInformation[] $additionalInformation
     */
    public function __construct(private readonly Substitution $substitution, private readonly bool $isFreeLessonType, private readonly array $students, array $absentStudentGroups, array $studentInfo, private readonly ?TimetableLesson $lesson, private readonly array $additionalInformation) {
        parent::__construct($absentStudentGroups, $studentInfo);
    }

    public function isFreeLesson(): bool {
        return $this->isFreeLessonType;
    }

    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    public function getTimetableLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    /**
     * @return TimetableLessonAdditionalInformation[]
     */
    public function getAdditionalInformation(): array {
        return $this->additionalInformation;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    public function getBlockName(): string {
        return 'substitution';
    }
}
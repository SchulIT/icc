<?php

namespace App\Dashboard;

use App\Entity\Student;
use App\Entity\Substitution;
use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;

class SubstitutionViewItem extends AbsenceAwareViewItem {

    public function __construct(private Substitution $substitution, private bool $isFreeLessonType, private array $students, array $absentStudentGroups, private readonly ?TimetableLesson $lesson, private readonly ?TeacherAbsenceLesson $absenceLesson) {
        parent::__construct($absentStudentGroups);
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

    public function getAbsenceLesson(): ?TeacherAbsenceLesson {
        return $this->absenceLesson;
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
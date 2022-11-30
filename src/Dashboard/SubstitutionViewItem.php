<?php

namespace App\Dashboard;

use App\Entity\Student;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;

class SubstitutionViewItem extends AbsenceAwareViewItem {

    public function __construct(private Substitution $substitution, private bool $isFreeLessonType, private array $students, array $absentStudentGroups, private readonly ?TimetableLesson $lesson) {
        parent::__construct($absentStudentGroups);
    }

    public function isFreeLesson(): bool {
        return $this->isFreeLessonType;
    }

    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    public function getTimetableLesson(): TimetableLesson {
        return $this->lesson;
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
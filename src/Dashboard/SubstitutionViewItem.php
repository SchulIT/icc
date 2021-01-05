<?php

namespace App\Dashboard;

use App\Entity\Student;
use App\Entity\Substitution;

class SubstitutionViewItem extends AbsenceAwareViewItem {

    /** @var bool */
    private $isFreeLessonType;

    private $substitution;

    /** @var Student[] List of affected students */
    private $students;

    public function __construct(Substitution $substitution, bool $isFreeLessonType, array $students, array $absentStudentGroups) {
        parent::__construct($absentStudentGroups);

        $this->substitution = $substitution;
        $this->isFreeLessonType = $isFreeLessonType;
        $this->students = $students;
    }

    public function isFreeLesson(): bool {
        return $this->isFreeLessonType;
    }

    /**
     * @return Substitution
     */
    public function getSubstitution(): Substitution {
        return $this->substitution;
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
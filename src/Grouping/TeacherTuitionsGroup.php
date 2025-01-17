<?php

namespace App\Grouping;

use App\Entity\Teacher;
use App\Entity\Tuition;

class TeacherTuitionsGroup implements GroupInterface, SortableGroupInterface {

    private Teacher $teacher;

    private bool $isGradeTeacher = false;

    /** @var Tuition[] */
    private array $tuitions = [ ];

    public function __construct(Teacher $teacher) {
        $this->teacher = $teacher;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    public function isGradeTeacher(): bool {
        return $this->isGradeTeacher;
    }

    public function setIsGradeTeacher(bool $isGradeTeacher): TeacherTuitionsGroup {
        $this->isGradeTeacher = $isGradeTeacher;
        return $this;
    }

    public function getKey() {
        return $this->teacher;
    }

    public function addItem($item) {
        $this->tuitions[] = $item;
    }

    public function &getItems(): array {
        return $this->tuitions;
    }
}
<?php

namespace App\Common\Grouping;

use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Teacher, Tuition>
 */
class TeacherTuitionsGroup implements SortableGroupInterface {
    private bool $isGradeTeacher = false;

    /** @var Tuition[] */
    private array $tuitions = [ ];

    public function __construct(private readonly Teacher $teacher) { }

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

    public function getKey(): Teacher {
        return $this->teacher;
    }

    public function addItem($item): void {
        $this->tuitions[] = $item;
    }

    public function &getItems(): array {
        return $this->tuitions;
    }
}
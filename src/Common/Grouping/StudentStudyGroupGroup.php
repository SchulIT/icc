<?php

namespace App\Common\Grouping;

use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<StudyGroup, Student>
 */
class StudentStudyGroupGroup implements GroupInterface, SortableGroupInterface {

    /** @var Student[] */
    private array $students = [ ];

    public function __construct(private readonly StudyGroup $studyGroup)
    {
    }

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    public function getStudents(): array {
        return $this->students;
    }

    public function getKey(): StudyGroup {
        return $this->studyGroup;
    }

    public function addItem($item): void {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }
}
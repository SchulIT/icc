<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\Student;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Grade|null, Student>
 */
class StudentGradeGroup implements GroupInterface, SortableGroupInterface {
    /**
     * @var Student[]
     */
    private array $students = [ ];

    public function __construct(private readonly ?Grade $grade)
    {
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @return Student[]
     */
    public function &getStudents(): array {
        return $this->students;
    }

    public function getKey(): ?Grade {
        return $this->grade;
    }

    public function addItem($item): void {
        $this->students[] = $item;
    }


    public function &getItems(): array {
        return $this->students;
    }
}
<?php

namespace App\ParentsDay;

use App\Entity\Student;

class TeacherOverview {
    /**
     * @param Student $student
     * @param TeacherItem[] $items
     */
    public function __construct(private readonly Student $student, private readonly array $items) {

    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return TeacherItem[]
     */
    public function getItems(): array {
        return $this->items;
    }
}
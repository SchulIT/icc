<?php

namespace App\Grouping;

use App\Entity\Appointment;
use App\Entity\BookEvent;
use App\Entity\Exam;
use App\Entity\Student;

class AbsentStudentGroup implements GroupInterface, SortableGroupInterface {

    /** @var Student[] */
    private $students;

    /**
     * @param BookEvent|Exam|null $objective
     */
    public function __construct(private readonly BookEvent|Exam|null $objective)
    {
    }

    /**
     * @return BookEvent|Exam|null
     */
    public function getObjective(): BookEvent|Exam|null {
        return $this->objective;
    }

    public function getKey() {
        return $this->objective;
    }

    public function addItem($item) {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }

    public function getStudents(): array {
        return $this->students;
    }

    public function isExam(): bool {
        return $this->objective instanceof Exam;
    }

    public function isBookEvent(): bool {
        return $this->objective instanceof BookEvent;
    }
}
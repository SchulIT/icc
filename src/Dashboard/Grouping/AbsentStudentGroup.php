<?php

namespace App\Dashboard\Grouping;

use App\Book\Entity\BookEvent;
use App\Common\Entity\Student;
use App\Exam\Entity\Exam;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

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
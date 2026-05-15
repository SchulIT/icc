<?php

namespace App\Exam\Grouping;

use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

class ExamStudentTuitionGroup implements GroupInterface, SortableGroupInterface {

    /** @var ExamStudent[] */
    private array $students = [ ];

    public function __construct(private readonly ?Tuition $tuition) {

    }

    /**
     * @return Tuition|null
     */
    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    /**
     * @return ExamStudent[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    public function getKey() {
        return $this->tuition;
    }

    public function addItem($item) {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }
}
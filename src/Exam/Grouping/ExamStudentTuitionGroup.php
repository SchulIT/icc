<?php

namespace App\Exam\Grouping;

use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Tuition|null, ExamStudent>
 */
class ExamStudentTuitionGroup implements SortableGroupInterface {

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

    public function getKey(): mixed {
        return $this->tuition;
    }

    public function addItem($item): void {
        $this->students[] = $item;
    }

    public function &getItems(): array {
        return $this->students;
    }
}
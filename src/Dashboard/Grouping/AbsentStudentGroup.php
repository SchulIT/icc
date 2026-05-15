<?php

namespace App\Dashboard\Grouping;

use App\Book\Entity\BookEvent;
use App\Dashboard\AbsentStudent;
use App\Exam\Entity\Exam;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<BookEvent|Exam, AbsentStudent>
 */
class AbsentStudentGroup implements SortableGroupInterface {

    /** @var AbsentStudent[] */
    private array $students;

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

    public function getKey(): null|Exam|BookEvent {
        return $this->objective;
    }

    public function addItem($item): void {
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
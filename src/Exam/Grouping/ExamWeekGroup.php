<?php

namespace App\Exam\Grouping;

use App\Exam\Entity\Exam;
use App\Framework\Date\WeekOfYear;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<WeekOfYear|null, Exam>
 */
class ExamWeekGroup implements SortableGroupInterface {

    /** @var Exam[] */
    private array $exams;

    public function __construct(private readonly ?WeekOfYear $weekOfYear)
    {
    }

    public function getWeekOfYear(): ?WeekOfYear {
        return $this->weekOfYear;
    }

    public function getKey(): ?WeekOfYear {
        return $this->weekOfYear;
    }

    public function addItem($item): void {
        $this->exams[] = $item;
    }

    public function &getItems(): array {
        return $this->exams;
    }

    public function getExams(): array {
        return $this->exams;
    }
}
<?php

namespace App\Exam\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use DateTime;
use App\Exam\Entity\Exam;

/**
 * @implements SortableGroupInterface<DateTime, Exam>
 */
class ExamDateGroup implements SortableGroupInterface {
    /**
     * @var Exam[]
     */
    private ?array $exams = null;

    public function __construct(private readonly DateTime $date)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return Exam[]
     */
    public function getExams(): ?array {
        return $this->exams;
    }

    /**
     * @return DateTime
     */
    public function getKey(): mixed {
        return $this->date;
    }

    /**
     * @param Exam $item
     */
    public function addItem($item): void {
        $this->exams[] = $item;
    }

    /**
     * @return Exam[]
     */
    public function &getItems(): array {
        return $this->exams;
    }
}
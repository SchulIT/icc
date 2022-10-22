<?php

namespace App\Grouping;

use DateTime;
use App\Entity\Exam;

class ExamDateGroup implements GroupInterface, SortableGroupInterface {
    /**
     * @var Exam[]
     */
    private ?array $exams = null;

    public function __construct(private DateTime $date)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return Exam[]
     */
    public function getExams() {
        return $this->exams;
    }

    /**
     * @return DateTime
     */
    public function getKey() {
        return $this->date;
    }

    /**
     * @param Exam $item
     */
    public function addItem($item) {
        $this->exams[] = $item;
    }

    /**
     * @return Exam[]
     */
    public function &getItems(): array {
        return $this->exams;
    }
}
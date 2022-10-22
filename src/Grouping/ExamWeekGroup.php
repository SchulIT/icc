<?php

namespace App\Grouping;

use App\Date\WeekOfYear;

class ExamWeekGroup implements GroupInterface, SortableGroupInterface {

    private $exams;

    public function __construct(private ?WeekOfYear $weekOfYear)
    {
    }

    public function getWeekOfYear() {
        return $this->weekOfYear;
    }

    public function getKey() {
        return $this->weekOfYear;
    }

    public function addItem($item) {
        $this->exams[] = $item;
    }

    public function &getItems(): array {
        return $this->exams;
    }

    public function getExams(): array {
        return $this->exams;
    }
}
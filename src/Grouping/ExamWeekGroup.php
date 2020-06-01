<?php

namespace App\Grouping;

class ExamWeekGroup implements GroupInterface, SortableGroupInterface {

    private $weekOfYear;
    private $exams;

    public function __construct(?WeekOfYear $weekOfYear) {
        $this->weekOfYear = $weekOfYear;
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
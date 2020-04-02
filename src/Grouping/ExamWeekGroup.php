<?php

namespace App\Grouping;

class ExamWeekGroup implements GroupInterface, SortableGroupInterface {

    private $week;
    private $exams;

    public function __construct(?int $week) {
        $this->week = $week;
    }

    public function getWeek() {
        return $this->week;
    }

    public function getKey() {
        return $this->week;
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
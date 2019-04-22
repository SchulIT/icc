<?php

namespace App\Grouping;

use App\Entity\Exam;

class ExamDateGroup implements GroupInterface, SortableGroupInterface {
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var Exam[]
     */
    private $exams;

    public function __construct(\DateTime $date) {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @return Exam[]
     */
    public function getExams() {
        return $this->exams;
    }

    /**
     * @return \DateTime
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
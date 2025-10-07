<?php

namespace App\ReturnItem;

use App\Entity\Student;
use DateTime;

readonly class Statistics {

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param int $itemsCount
     * @param array<int, Row> $rows
     * @param DateTime $createdAt
     */
    public function __construct(
        public DateTime $start,
        public DateTime $end,
        public int $itemsCount,
        public array $rows,
        public DateTime $createdAt
    ) {

    }

    public function getStudentRow(Student $student): ?Row {
        return $this->rows[$student->getId()] ?? null;
    }

    public function getStudentIds(): array {
        $ids = [ ];
        foreach($this->rows as $row) {
            $ids[] = $row->studentId;
        }
        return $ids;
    }
}
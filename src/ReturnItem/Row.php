<?php

namespace App\ReturnItem;

class Row {
    public function __construct(
        public readonly int $studentId,
        public int $itemsCount,
        public string|null $grade = null
    ) {

    }
}
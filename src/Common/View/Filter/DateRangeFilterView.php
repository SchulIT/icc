<?php

namespace App\Common\View\Filter;

use DateTime;

readonly class DateRangeFilterView {
    public function __construct(
        public DateTime|null $start = null,
        public DateTime|null $end = null,
    ) {
    }
}
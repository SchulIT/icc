<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use DateTime;

readonly class DateRangeFilter {
    public function handle(
        DateTime|null $start,
        DateTime|null $end,
        Section|null $section = null,
        int|null $maximumDays = null
    ): DateRangeFilterView {
        if($start === null || $end === null) {
            return new DateRangeFilterView(null, null);
        }

        if($start > $end) {
            return $this->handle($end, $start, $section, $maximumDays);
        }

        $diff = $start->diff($end);

        if($maximumDays !== null && $maximumDays > 0 && abs($diff->d) > $maximumDays) {
            $end = (clone $start)->modify('+'.$maximumDays.' days');
        }

        if($section !== null) {
            $start = max($section->getStart(), $start);
            $end = min($section->getEnd(), $end);
        }

        return new DateRangeFilterView($start, $end);
    }
}
<?php

namespace App\Book\Student\Cache;

use DateTime;

class StudentInfoCounts {

    /**
     * @param array<int, int> $flags Key: Flag ID, Value: Count
     */
    public function __construct(public int $numComments,
                                public int $lateMinutes,
                                public int $absentLessons,
                                public int $totalLessons,
                                public int $notExcusedLessons,
                                public int $notSetLessons,
                                public array $flags,
                                public DateTime $createdAt) { }
}
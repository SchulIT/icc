<?php

namespace App\Book\Statistics;

class BookLessonCount {
    public function __construct(public int $holdLessonsCount, public int $missingLessonsCount) { }
}
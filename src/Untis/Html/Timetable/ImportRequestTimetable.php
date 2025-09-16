<?php

namespace App\Untis\Html\Timetable;

readonly class ImportRequestTimetable {

    /**
     * @param array $weeks
     * @param string[] $gradeLessons
     * @param string[] $subjectLessons
     */
    public function __construct(public array $weeks, public array $gradeLessons, public array $subjectLessons) { }
}
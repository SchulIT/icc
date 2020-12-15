<?php

namespace App\Migrations;

use App\Timetable\TimetableTimeHelper;

interface TimetableTimeHelperDependentMigrationInterface {
    public function setTimetableTimeHelper(TimetableTimeHelper $timetableTimeHelper): void;
}
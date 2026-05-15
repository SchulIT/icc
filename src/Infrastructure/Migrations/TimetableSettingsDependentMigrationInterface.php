<?php

namespace App\Infrastructure\Migrations;

use App\Timetable\Settings\TimetableSettings;

interface TimetableSettingsDependentMigrationInterface {
    public function setTimetableSettings(TimetableSettings $settings): void;
}
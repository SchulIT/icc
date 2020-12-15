<?php

namespace App\Migrations;

use App\Settings\TimetableSettings;

interface TimetableSettingsDependentMigrationInterface {
    public function setTimetableSettings(TimetableSettings $settings): void;
}
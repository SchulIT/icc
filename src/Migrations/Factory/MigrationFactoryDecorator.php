<?php

namespace App\Migrations\Factory;

use App\Migrations\TimetableSettingsDependentMigrationInterface;
use App\Migrations\TimetableTimeHelperDependentMigrationInterface;
use App\Settings\TimetableSettings;
use App\Timetable\TimetableTimeHelper;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;

class MigrationFactoryDecorator implements MigrationFactory {
    public function __construct(private MigrationFactory $migrationFactory, private TimetableSettings $timetableSettings, private TimetableTimeHelper $timetableTimeHelper)
    {
    }

    public function createVersion(string $migrationClassName): AbstractMigration {
        $instance = $this->migrationFactory->createVersion($migrationClassName);

        if($instance instanceof TimetableSettingsDependentMigrationInterface) {
            $instance->setTimetableSettings($this->timetableSettings);
        }

        if($instance instanceof TimetableTimeHelperDependentMigrationInterface) {
            $instance->setTimetableTimeHelper($this->timetableTimeHelper);
        }

        return $instance;
    }
}
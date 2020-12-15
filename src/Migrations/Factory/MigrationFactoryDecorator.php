<?php

namespace App\Migrations\Factory;

use App\Migrations\TimetableSettingsDependentMigrationInterface;
use App\Migrations\TimetableTimeHelperDependentMigrationInterface;
use App\Settings\TimetableSettings;
use App\Timetable\TimetableTimeHelper;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;

class MigrationFactoryDecorator implements MigrationFactory {
    private $migrationFactory;

    private $timetableSettings;
    private $timetableTimeHelper;

    public function __construct(MigrationFactory $migrationFactory, TimetableSettings $timetableSettings, TimetableTimeHelper $timeHelper)
    {
        $this->migrationFactory = $migrationFactory;
        $this->timetableSettings = $timetableSettings;
        $this->timetableTimeHelper = $timeHelper;
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
<?php

namespace App\Migrations\Factory;

use App\Exam\ExamStudentsResolver;
use App\Migrations\ExamRepositoryDependantMigrationInterface;
use App\Migrations\ExamStudentsResolverDependentMigrationInterface;
use App\Migrations\SettingsManagerDependentMigrationInterface;
use App\Migrations\TimetableSettingsDependentMigrationInterface;
use App\Migrations\TimetableTimeHelperDependentMigrationInterface;
use App\Repository\ExamRepositoryInterface;
use App\Settings\SettingsManager;
use App\Settings\TimetableSettings;
use App\Timetable\TimetableTimeHelper;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;

class MigrationFactoryDecorator implements MigrationFactory {
    public function __construct(private readonly MigrationFactory $migrationFactory,
                                private readonly TimetableSettings $timetableSettings,
                                private readonly TimetableTimeHelper $timetableTimeHelper,
                                private readonly ExamStudentsResolver $resolver,
                                private readonly ExamRepositoryInterface $examRepository)
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

        if($instance instanceof ExamStudentsResolverDependentMigrationInterface) {
            $instance->setExamStudentsResolver($this->resolver);
        }

        if($instance instanceof ExamRepositoryDependantMigrationInterface) {
            $instance->setExamRepository($this->examRepository);
        }

        return $instance;
    }
}
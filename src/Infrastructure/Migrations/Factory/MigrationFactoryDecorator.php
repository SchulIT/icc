<?php

namespace App\Infrastructure\Migrations\Factory;

use App\Exam\ExamStudentsResolver;
use App\Infrastructure\Migrations\ExamRepositoryDependantMigrationInterface;
use App\Infrastructure\Migrations\ExamStudentsResolverDependentMigrationInterface;
use App\Infrastructure\Migrations\TimetableSettingsDependentMigrationInterface;
use App\Infrastructure\Migrations\TimetableTimeHelperDependentMigrationInterface;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Framework\Settings\SettingsManager;
use App\Timetable\Settings\TimetableSettings;
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
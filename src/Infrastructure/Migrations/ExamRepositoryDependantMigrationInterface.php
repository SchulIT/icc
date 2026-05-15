<?php

namespace App\Infrastructure\Migrations;

use App\Exam\Repository\ExamRepositoryInterface;

interface ExamRepositoryDependantMigrationInterface {
    public function setExamRepository(ExamRepositoryInterface $examRepository): void;
}
<?php

namespace App\Migrations;

use App\Repository\ExamRepositoryInterface;

interface ExamRepositoryDependantMigrationInterface {
    public function setExamRepository(ExamRepositoryInterface $examRepository): void;
}
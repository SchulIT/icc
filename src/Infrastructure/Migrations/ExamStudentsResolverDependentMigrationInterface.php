<?php

namespace App\Infrastructure\Migrations;

use App\Exam\ExamStudentsResolver;

interface ExamStudentsResolverDependentMigrationInterface {
    public function setExamStudentsResolver(ExamStudentsResolver $resolver): void;
}
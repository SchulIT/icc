<?php

namespace App\Import\External\ParentsDayTeacherRoom;

readonly class ImportResult {
    public function __construct(
        public array $ignoredTeachers,
        public array $ignoredRooms,
        public int $importCount
    ) { }
}
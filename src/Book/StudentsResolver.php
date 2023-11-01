<?php

namespace App\Book;

use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Settings\BookSettings;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;

class StudentsResolver {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly BookSettings $bookSettings, private readonly Sorter $sorter) { }

    public function resolve(Tuition $tuition, bool $includeStudentsExcludedByStatus = false, bool $includeStudentsWithAttendance = false) {
        $exclude = $this->bookSettings->getExcludeStudentsStatus();

        if($includeStudentsExcludedByStatus === true) {
            $exclude = [ ];
        }

        $students = $this->studentRepository->findAllByTuition($tuition, $exclude, $includeStudentsWithAttendance);
        $this->sorter->sort($students, StudentStrategy::class);

        return $students;
    }
}
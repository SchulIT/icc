<?php

namespace App\Book;

use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Settings\BookSettings;

class StudentsResolver {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, bool $includeStudentsExcludedByStatus = false, bool $includeStudentsWithAttendance = false) {
        $exclude = $this->bookSettings->getExcludeStudentsStatus();

        if($includeStudentsExcludedByStatus === true) {
            $exclude = [ ];
        }

        return $this->studentRepository->findAllByTuition($tuition, $exclude, $includeStudentsWithAttendance);
    }
}
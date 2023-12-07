<?php

namespace App\Book;

use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Settings\BookSettings;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StudentsResolver {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly BookSettings $bookSettings, private readonly Sorter $sorter) { }

    /**
     * @param Tuition $tuition
     * @param bool $includeStudentsExcludedByStatus
     * @param bool $includeStudentsWithAttendance
     * @return Student[]
     */
    public function resolve(Tuition $tuition, bool $includeStudentsExcludedByStatus = false, bool $includeStudentsWithAttendance = false): array {
        $exclude = $this->bookSettings->getExcludeStudentsStatus();

        if($includeStudentsExcludedByStatus === true) {
            $exclude = [ ];
        }

        $students = $this->studentRepository->findAllByTuition($tuition, $exclude, $includeStudentsWithAttendance);
        $this->sorter->sort($students, StudentStrategy::class);

        return $students;
    }

    public function resolvePaginated(int $itemsPerPage, int &$page, Tuition $tuition, bool $includeStudentsExcludedByStatus = false, bool $includeStudentsWithAttendance = false): Paginator {
        $exclude = $this->bookSettings->getExcludeStudentsStatus();

        if($includeStudentsExcludedByStatus === true) {
            $exclude = [ ];
        }

        return $this->studentRepository->getStudentsByTuitionPaginator($itemsPerPage, $page, $tuition, $exclude, $includeStudentsWithAttendance);
    }
}
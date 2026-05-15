<?php

namespace App\Book;

use App\Common\Entity\Student;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\Tuition;
use App\Common\Repository\StudentRepositoryInterface;
use App\Book\Settings\BookSettings;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentStrategy;
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
        if($tuition->getId() === null) {
            $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();
        } else {
            $exclude = $this->bookSettings->getExcludeStudentsStatus();

            if ($includeStudentsExcludedByStatus === true) {
                $exclude = [];
            }

            $students = $this->studentRepository->findAllByTuition($tuition, $exclude, $includeStudentsWithAttendance);
        }
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
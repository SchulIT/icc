<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsenceReason;
use App\Dashboard\AbsentStudent;
use App\Entity\Student;
use App\Repository\AbsenceRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;

class AbsentStudentResolver implements AbsenceResolveStrategyInterface {

    private $absenceRepository;

    public function __construct(AbsenceRepositoryInterface $absenceRepository) {
        $this->absenceRepository = $absenceRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        return array_map(
            function(Student $student) {
                return new AbsentStudent($student, AbsenceReason::Other());
            },
            $this->absenceRepository->findAllStudentsByDateAndLesson($dateTime, ArrayUtils::iterableToArray($students), $lesson)
        );
    }
}
<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsenceReason;
use App\Dashboard\AbsentStudent;
use App\Common\Entity\Student;
use App\Substitution\Repository\AbsenceRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use DateTime;

/**
 * Resolves absent students which are part of an absent study group (which is imported e.g. from Untis)
 */
class AbsentStudyGroupResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private AbsenceRepositoryInterface $absenceRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        return array_map(
            fn(Student $student) => new AbsentStudent($student, AbsenceReason::Other),
            $this->absenceRepository->findAllStudentsByDateAndLesson($dateTime, ArrayUtils::iterableToArray($students), $lesson)
        );
    }
}
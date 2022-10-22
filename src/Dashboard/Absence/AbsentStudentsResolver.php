<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentStudentWithAbsenceNote;
use App\Entity\StudentAbsence;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;

/**
 * Resolves absent students from an absent note made by parents/students/teachers
 */
class AbsentStudentsResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private StudentAbsenceRepositoryInterface $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $students = ArrayUtils::iterableToArray($students);
        $absences = $this->repository->findByStudents($students, null, $dateTime, $lesson);

        $absences = array_filter($absences, fn(StudentAbsence $absence) => $absence->getType()->isMustApprove() === false || $absence->isApproved());

        return array_map(fn(StudentAbsence $note) => new AbsentStudentWithAbsenceNote($note->getStudent(), $note), $absences);
    }
}
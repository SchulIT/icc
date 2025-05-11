<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentStudent;
use App\Entity\Student;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.absence_resolver')]
interface AbsenceResolveStrategyInterface {
    /**
     * @param Student[] $students
     * @return AbsentStudent[]
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array;
}
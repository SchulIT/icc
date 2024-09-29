<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentBookEventStudent;
use App\Repository\BookEventRepositoryInterface;
use DateTime;

class BookEventStudentsResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private readonly BookEventRepositoryInterface $bookEventRepository) {

    }

    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $result = [ ];

        foreach($students as $student) {
            foreach($this->bookEventRepository->findByStudent($student, $dateTime, $dateTime) as $event) {
                if($event->getLessonStart() <= $lesson && $lesson <= $event->getLessonEnd()) {
                    $result[] = new AbsentBookEventStudent($student, $event);
                }
            }
        }

        return $result;
    }
}
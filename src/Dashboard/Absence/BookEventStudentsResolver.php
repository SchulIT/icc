<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentBookEventStudent;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Repository\BookEventRepositoryInterface;
use DateTime;

class BookEventStudentsResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private readonly BookEventRepositoryInterface $bookEventRepository, private readonly FeatureManager $featureManager) {

    }

    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        if($this->featureManager->isFeatureEnabled(Feature::Book) !== true) {
            return [ ];
        }

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
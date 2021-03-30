<?php

namespace App\Timetable;

use App\Repository\TimetableWeekRepositoryInterface;
use DateTime;
use App\Entity\TimetableWeek as TimetableWeekEntity;

class TimetableWeekHelper {

    private $timetableWeekRepository;

    public function __construct(TimetableWeekRepositoryInterface $weekRepository) {
        $this->timetableWeekRepository = $weekRepository;
    }

    public function getTimetableWeek(DateTime $dateTime): ?TimetableWeekEntity {
        $all = $this->timetableWeekRepository->findAll();
        $count = count($all);

        if($count === 0) {
            return null;
        }

        $weekNumber = (int)$dateTime->format('W');

        foreach($all as $week) {
            if(in_array($weekNumber, $week->getWeeksAsIntArray())) {
                return $week;
            }
        }

        return null;
    }

    public function isTimetableWeek(DateTime $dateTime, ?TimetableWeekEntity $week): bool {
        if($week === null) {
            return false;
        }

        return in_array((int)$dateTime->format('W'), $week->getWeeksAsIntArray());
    }
}
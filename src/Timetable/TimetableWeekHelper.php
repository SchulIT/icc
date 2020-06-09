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
        $mod = (int)$dateTime->format('W') % $count;

        foreach($all as $week) {
            if($week->getWeekMod() === $mod) {
                return $week;
            }
        }

        return null;
    }

    public function isTimetableWeek(DateTime $dateTime, TimetableWeekEntity $week): bool {
        $all = $this->timetableWeekRepository->findAll();
        $count = count($all);

        return (int)$dateTime->format('W') % $count === $week->getWeekMod();
    }
}
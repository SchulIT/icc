<?php

namespace App\Controller;

use DateInterval;
use DateMalformedStringException;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;

trait DateTimeHelperTrait {
    private function getTodayOrNextDay(DateHelper $dateHelper, ?string $threshold): DateTime {
        $today = $dateHelper->getToday();

        if(empty($threshold)) {
            return $today;
        }

        [$hour, $minute] = explode(':', $threshold);

        try {
            $interval = new DateInterval(sprintf('PT%dH%dM', $hour, $minute));
            $threshold = (clone $today)->add($interval);
        } catch (Exception) {
            $threshold = $today;
        }

        if($dateHelper->getNow() > $threshold) {
            $today = $today->modify('+1 day');
        }

        return $today;
    }

    /**
     * @param DateHelper $dateHelper
     * @param int $numberOfDays
     * @param bool $skipWeekends
     * @param DateTime|null $today
     * @return DateTime[]
     * @throws \DateMalformedStringException
     */
    private function getListOfNextDays(DateHelper $dateHelper, int $numberOfDays, bool $skipWeekends, DateTime|null $today = null): array {
        if($today === null) {
            $today = $dateHelper->getToday();
        }

        if($skipWeekends) {
            // Ensure to start at a weekday in case weekends are skipped
            while ($today->format('N') >= 6) {
                $today->modify('+1 day');
            }
        }

        $days = [ $today ];
        $last = $today;

        while(count($days) < $numberOfDays) {
            $day = clone $last;
            $day->modify('+1 day');

            if($skipWeekends === false || $day->format('N') < 6) {
                $days[] = $day;
            }

            $last = $day;
        }

        return $days;
    }

    /**
     * @param DateTime[] $dateTimes
     * @throws DateMalformedStringException
     */
    private function getCurrentDate(array $dateTimes, ?string $date): ?DateTime {
        if(count($dateTimes) === 0) {
            return null;
        }

        if($date === null) {
            return $dateTimes[0];
        }

        $selectedDateTime = new DateTime($date);

        foreach($dateTimes as $dateTime) {
            if($dateTime == $selectedDateTime) {
                return $dateTime;
            }
        }

        return $dateTimes[0];
    }
}
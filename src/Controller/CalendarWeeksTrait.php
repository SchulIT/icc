<?php

namespace App\Controller;

use App\Entity\Section;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;

trait CalendarWeeksTrait {

    private function getClosestWeekStart(DateTime $dateTime): DateTime {
        $dateTime = clone $dateTime;

        while((int)$dateTime->format('N') > 1) {
            $dateTime = $dateTime->modify('-1 day');
        }

        return $dateTime;
    }

    /**
     * @return DateTime[] All mondays with their week numbers as key
     */
    private function listCalendarWeeks(DateTime $start, DateTime $end): array {
        $weekStarts = [ ];
        $current = $this->getClosestWeekStart($start);

        while($current < $end) {
            $weekStarts[(int)$current->format('W')] = clone $current;
            $current = $current->modify('+7 days');
        }

        return $weekStarts;
    }

    private function resolveSelectedDate(Request $request, ?Section $currentSection, DateHelper $dateHelper): ?DateTime {
        $selectedDate = null;
        try {
            if($request->query->has('date')) {
                $selectedDate = new DateTime($request->query->get('date', null));
                $selectedDate->setTime(0, 0, 0);
            }
        } catch (Exception) {
            $selectedDate = null;
        }

        if($selectedDate === null && $currentSection !== null) {
            $selectedDate = $this->getClosestWeekStart($dateHelper->getToday());
        }

        if($selectedDate !== null && $currentSection !== null && $dateHelper->isBetween($selectedDate, $currentSection->getStart(), $currentSection->getEnd()) !== true) {
            if($selectedDate < $currentSection->getStart()) {
                $selectedDate = $this->getClosestWeekStart($currentSection->getStart());
            } else {
                $selectedDate = $this->getClosestWeekStart($currentSection->getEnd());
            }
        }

        return $selectedDate;
    }
}
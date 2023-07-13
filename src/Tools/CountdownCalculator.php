<?php

namespace App\Tools;

use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;

class CountdownCalculator {
    public function __construct(private readonly DateHelper $dateHelper, private readonly TimetableSettings $timetableSettings,
                                private readonly AppointmentRepositoryInterface $appointmentRepository, private readonly AppointmentCategoryRepositoryInterface $appointmentCategoryRepository) {

    }

    /**
     * @return DateTime[]
     */
    public function getFreeDays(): array {
        $freeDays = [ ];
        $categories = [ ];

        foreach($this->timetableSettings->getCategoryIds() as $id) {
            $category = $this->appointmentCategoryRepository->findOneById($id);

            if($category !== null) {
                $categories[] = $category;
            }
        }

        $appointments = $this->appointmentRepository->findAll($categories);

        foreach($appointments as $appointment) {
            $current = (clone $appointment->getStart());

            while($current < $appointment->getEnd()) {
                $freeDays[] = $current;
                $current = (clone $current)->modify('+1 day');
            }
        }

        return ArrayUtils::unique($freeDays);
    }

    public function computeSchoolDaysUntis(DateTime $target): int {
        return count(
            $this->getAllDatesBetween(
                $this->dateHelper->getToday(),
                $target,
                $this->timetableSettings->getDays(),
                $this->getFreeDays()
            )
        );
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param int[] $weekDays Only return those dates which are of any day in this array (0 = sunday, 1 = monday, ...)
     * @param DateTime[] $exclude Days to exclude
     * @return DateTime[]
     */
    private function getAllDatesBetween(DateTime $start, DateTime $end, array $weekDays, array $exclude): array {
        $range = [ ];

        $current = clone $start;
        while($current < $end) {
            if(in_array((int)$current->format('w'), $weekDays) && !in_array($current, $exclude)) {
                $range[] = clone $current;
            }
            $current = $current->modify('+1 day');
        }

        return $range;
    }
}
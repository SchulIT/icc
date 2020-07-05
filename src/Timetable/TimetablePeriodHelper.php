<?php

namespace App\Timetable;

use App\Entity\TimetablePeriod;
use App\Entity\UserType;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Security\Voter\TimetablePeriodVoter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetablePeriodHelper {

    private $timetablePeriodRepository;
    private $authorizationChecker;
    private $dateHelper;

    public function __construct(TimetablePeriodRepositoryInterface $timetablePeriodRepository, AuthorizationCheckerInterface $authorizationChecker, DateHelper $dateHelper) {
        $this->timetablePeriodRepository = $timetablePeriodRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->dateHelper = $dateHelper;
    }

    /**
     * Returns all periods a given user is allowed to view.
     *
     * @return TimetablePeriod[]
     */
    public function getPeriods(): array {
        $periods = $this->timetablePeriodRepository->findAll();
        $allowedPeriods = [ ];

        foreach($periods as $period) {
            if($this->authorizationChecker->isGranted(TimetablePeriodVoter::View, $period)) {
                $allowedPeriods[] = $period;
            }
        }

        return $allowedPeriods;
    }

    /**
     * Returns the period a given date belongs to in case the given user type is allowed to view it.
     * Returns null in case the user is not allowed to view the period or the date does not belong to any period.
     *
     * @param DateTime $dateTime
     * @return TimetablePeriod|null
     */
    public function getPeriod(DateTime $dateTime): ?TimetablePeriod {
        $periods = $this->getPeriods();

        foreach($periods as $period) {
            if($this->dateHelper->isBetween($dateTime, $period->getStart(), $period->getEnd())) {
                return $period;
            }
        }

        return null;
    }
}
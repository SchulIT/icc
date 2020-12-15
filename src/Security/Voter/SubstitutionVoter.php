<?php

namespace App\Security\Voter;

use App\Entity\Substitution;
use App\Settings\SubstitutionSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubstitutionVoter extends Voter {

    public const View = 'view';

    private $dateHelper;
    private $substitutionSettings;

    public function __construct(DateHelper $dateHelper, SubstitutionSettings $substitutionSettings) {
        $this->dateHelper = $dateHelper;
        $this->substitutionSettings = $substitutionSettings;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::View && $subject instanceof Substitution;
    }

    /**
     * @param string $attribute
     * @param Substitution $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $threshold = $this->getDateThreshold();

        return $subject->getDate() <= $threshold;
    }

    private function getDateThreshold(): \DateTime {
        $today = $this->dateHelper->getToday();
        $numberOfDays = $this->substitutionSettings->getNumberOfAheadDaysForSubstitutions();
        $skipWeekends = $this->substitutionSettings->skipWeekends();

        $daysAdded = 0;

        while($daysAdded < $numberOfDays) {
            $today->modify('+1 day');

            if($skipWeekends === false || $today->format('N') < 6) {
                $daysAdded++;
            }
        }

        return $today;
    }
}
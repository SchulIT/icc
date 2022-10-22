<?php

namespace App\Security\Voter;

use DateTime;
use App\Entity\Substitution;
use App\Settings\SubstitutionSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubstitutionVoter extends Voter {

    public const View = 'view';

    public function __construct(private DateHelper $dateHelper, private SubstitutionSettings $substitutionSettings)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::View && $subject instanceof Substitution;
    }

    /**
     * @param Substitution $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        $threshold = $this->getDateThreshold();

        return $subject->getDate() <= $threshold;
    }

    private function getDateThreshold(): DateTime {
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
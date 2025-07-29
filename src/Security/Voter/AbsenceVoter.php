<?php

namespace App\Security\Voter;

use App\Entity\Absence;
use App\Entity\User;
use App\Settings\SubstitutionSettings;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AbsenceVoter extends Voter {

    public const View = 'view';
    public const ViewAny = 'view-absences';

    public function __construct(private SubstitutionSettings $substitutionSettings)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::ViewAny || ($attribute === self::View && $subject instanceof Absence);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->substitutionSettings->areAbsencesVisibleFor($user->getUserType());
    }
}
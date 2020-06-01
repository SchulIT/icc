<?php

namespace App\Security\Voter;

use App\Entity\Absence;
use App\Entity\User;
use App\Settings\SubstitutionSettings;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AbsenceVoter extends Voter {

    public const View = 'view';
    public const ViewAny = 'view-absences';

    private $substitutionSettings;

    public function __construct(SubstitutionSettings $substitutionSettings) {
        $this->substitutionSettings = $substitutionSettings;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::ViewAny || ($attribute === static::View && $subject instanceof Absence);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->substitutionSettings->areAbsencesVisibleFor($user->getUserType());
    }
}
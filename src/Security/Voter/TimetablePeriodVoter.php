<?php

namespace App\Security\Voter;

use App\Entity\TimetablePeriod;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetablePeriodVoter extends Voter {

    const New = 'new-timetable-period';
    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Edit,
            static::Remove,
            static::View
        ];

        return $attribute === static::New
            || (in_array($attribute, $attributes) && $subject instanceof TimetablePeriod);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        // TODO: Implement voteOnAttribute() method.
    }
}
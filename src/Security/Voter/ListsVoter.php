<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ListsVoter extends Voter {

    public const Teachers = 'teachers';
    public const Students = 'students';
    public const Tuitions = 'tuitions';
    public const StudyGroups = 'studygroups';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Teachers,
            static::Students,
            static::Tuitions,
            static::StudyGroups
        ];

        return in_array($attribute, $attributes)
            && $subject === null;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        // TODO
        return true;
    }
}
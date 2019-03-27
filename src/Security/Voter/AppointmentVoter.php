<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AppointmentVoter extends Voter {

    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        // TODO: Implement supports() method.
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        // TODO: Implement voteOnAttribute() method.
    }
}
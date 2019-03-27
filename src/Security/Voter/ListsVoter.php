<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ListsVoter extends Voter {

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
<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter {

    const New = 'new-message';
    const View = 'view';
    const Edit = 'edit';
    const Remove = 'remove';
    const Confirm = 'confirm';
    const Dismiss = 'dismiss';

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
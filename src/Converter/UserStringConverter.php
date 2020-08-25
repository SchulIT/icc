<?php

namespace App\Converter;

use App\Entity\User;

class UserStringConverter {
    public function convert(?User $user) {
        if($user === null) {
            return 'unknown';
        }

        if(empty($user->getFirstname()) || empty($user->getLastname())) {
            return $user->getUsername();
        }

        return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
    }
}
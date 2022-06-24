<?php

namespace App\Converter;

use App\Entity\User;

class UserStringConverter {
    public function convert(?User $user, bool $includeUsername = true): string {
        if($user === null) {
            return 'unknown';
        }

        if(empty($user->getFirstname()) || empty($user->getLastname())) {
            return $user->getUsername();
        }

        if($includeUsername === false) {
            return sprintf('%s, %s', $user->getLastname(), $user->getFirstname());
        }

        return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
    }
}
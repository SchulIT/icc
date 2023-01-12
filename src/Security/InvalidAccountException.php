<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Throwable;

class InvalidAccountException extends CustomUserMessageAccountStatusException {
    public function __construct(string $messageKey, array $messageData = [ ], $code = 0, Throwable $previous = null) {
        parent::__construct($messageKey, $messageData, $code, $previous);
    }

}
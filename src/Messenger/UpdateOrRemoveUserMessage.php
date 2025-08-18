<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class UpdateOrRemoveUserMessage {
    public function __construct(private int $userId) {    }
    
    public function getUserId(): int {
        return $this->userId;
    }
}
<?php

namespace App\Messenger;

readonly class UpdateOrRemoveUserMessage {
    public function __construct(private int $userId) {    }
    
    public function getUserId(): int {
        return $this->userId;
    }
}
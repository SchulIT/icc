<?php

namespace App\Messenger;

class UpdateOrRemoveUserMessage {
    public function __construct(private readonly int $userId) {    }
    
    public function getUserId(): int {
        return $this->userId;
    }
}
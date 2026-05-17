<?php

namespace App\Message\Messenger;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class RemoveMessageMessage {
    public function __construct(
        public int $messageId
    ) {

    }
}
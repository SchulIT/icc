<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class SendPushoverNotificationMessage {
    public function __construct(public int     $recipientId,
                                public string  $recipientUserIdentifier,
                                public string  $content,
                                public ?string $subject,
                                public ?string $link = null,
                                public ?string $linkText = null) {
    }

}
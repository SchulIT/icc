<?php

namespace App\Messenger;

class SendPushoverNotificationMessage {
    public function __construct(public readonly int $recipientId,
                                public readonly string $recipientUserIdentifier,
                                public readonly string $content,
                                public readonly ?string $subject,
                                public readonly ?string $link = null,
                                public readonly ?string $linkText = null) {
    }

}
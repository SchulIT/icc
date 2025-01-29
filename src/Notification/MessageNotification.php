<?php

namespace App\Notification;

use App\Entity\Message;
use App\Entity\User;

class MessageNotification extends Notification {
    public function __construct(string $type, User $recipient, string $subject, string $content, ?string $link, ?string $linkText, private readonly Message $message) {
        parent::__construct($type, $recipient, $subject, $content, $link, $linkText);
    }

    public function getMessage(): Message {
        return $this->message;
    }
}
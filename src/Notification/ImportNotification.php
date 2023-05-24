<?php

namespace App\Notification;

use App\Entity\User;

class ImportNotification extends Notification {
    public function __construct(User $recipient, string $subject, string $content, ?string $link, ?string $linkText, private readonly ?string $sender, private readonly ?string $replyTo) {
        parent::__construct($recipient, $subject, $content, $link, $linkText);
    }

    /**
     * @return string|null
     */
    public function getReplyTo(): ?string {
        return $this->replyTo;
    }

    /**
     * @return string|null
     */
    public function getSender(): ?string {
        return $this->sender;
    }
}
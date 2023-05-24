<?php

namespace App\Notification;

use App\Entity\User;
use DateTime;

class Notification {

    private DateTime $createdAt;

    public function __construct(private readonly User $recipient, private readonly string $subject, private readonly string $content,
                                private ?string $link, private readonly ?string $linkText, private readonly bool $enforceDelivery = false) {
        $this->createdAt = new DateTime();
    }

    public function getRecipient(): User {
        return $this->recipient;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getLink(): ?string {
        return $this->link;
    }

    public function setLink(?string $link): Notification {
        $this->link = $link;
        return $this;
    }

    public function getLinkText(): ?string {
        return $this->linkText;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function isDeliveryEnforced(): bool {
        return $this->enforceDelivery;
    }
}
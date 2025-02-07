<?php

namespace App\Notification;

use App\Entity\User;
use DateTime;

class Notification {

    private DateTime $createdAt;

    public function __construct(private readonly string $type, private readonly User $recipient, private readonly string $subject, private readonly string $content,
                                private ?string $link, private readonly ?string $linkText, private readonly array $namesToErase = [ ]) {
        $this->createdAt = new DateTime();
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
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

    public function getSafeSubject(): string {
        $subject = $this->getSubject();

        foreach($this->namesToErase as $name => $replacement) {
            $subject = str_replace($name, $replacement, $subject);
        }

        return $subject;
    }

    public function getSafeContent(): string {
        $content = $this->getContent();

        foreach($this->namesToErase as $name => $replacement) {
            $content = str_replace($name, $replacement, $content);
        }

        return $content;
    }
}
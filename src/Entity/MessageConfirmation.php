<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
class MessageConfirmation {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'confirmations')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Message $message = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function getMessage(): Message {
        return $this->message;
    }

    public function setMessage(Message $message): MessageConfirmation {
        $this->message = $message;
        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): MessageConfirmation {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

}
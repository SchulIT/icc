<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 */
class MessageConfirmation {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="confirmations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Message $message = null;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTime $createdAt;

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
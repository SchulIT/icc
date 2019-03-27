<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MessageAttachment {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Message
     */
    private $message;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $filename;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessageAttachment
     */
    public function setMessage(Message $message): MessageAttachment {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return MessageAttachment
     */
    public function setFilename(string $filename): MessageAttachment {
        $this->filename = $filename;
        return $this;
    }

}
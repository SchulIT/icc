<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class MessageFile {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Message
     */
    private $message;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $label;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $extension;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessageFile
     */
    public function setMessage(Message $message): MessageFile {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return MessageFile
     */
    public function setLabel(?string $label): MessageFile {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string {
        return $this->extension;
    }

    /**
     * @param string|null $extension
     * @return MessageFile
     */
    public function setExtension(?string $extension): MessageFile {
        $this->extension = $extension;
        return $this;
    }
}
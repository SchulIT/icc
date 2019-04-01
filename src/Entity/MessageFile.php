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
     * @ORM\Column(type="json_array")
     * @Assert\Length(min="1")
     * @var string[]
     */
    private $extensions;

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
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     * @return MessageFile
     */
    public function setLabel(string $label): MessageFile {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array {
        return $this->extensions;
    }

    /**
     * @param string[] $extensions
     * @return MessageFile
     */
    public function setExtensions(array $extensions): MessageFile {
        $this->extensions = $extensions;
        return $this;
    }
}
<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Auditable()
 * @Vich\Uploadable()
 */
class MessageAttachment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Message $message = null;

    /**
     * @Vich\UploadableField(mapping="messages", fileNameProperty="path", originalName="filename", size="size")
     */
    private $file;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $filename = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $path = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $size = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTime $updatedAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getMessage(): ?Message {
        return $this->message;
    }

    public function setMessage(Message $message): MessageAttachment {
        $this->message = $message;
        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file = null): MessageAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }

    public function setFilename(?string $filename): MessageAttachment {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(?string $path): MessageAttachment {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
        return $this->size;
    }

    public function setSize(?int $size): MessageAttachment {
        $this->size = $size;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
}
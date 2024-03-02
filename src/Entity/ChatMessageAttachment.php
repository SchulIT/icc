<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Vich\Uploadable]
class ChatMessageAttachment {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: ChatMessage::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ChatMessage $message;

    #[Vich\UploadableField(mapping: 'chat', fileNameProperty: 'path', size: 'size', originalName: 'filename')]
    #[Assert\File(maxSize: '5M', extensions: ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => ['image/jpg', 'image/jpeg'], 'jpeg' => ['image/jpg', 'image/jpeg'] ])]
    private ?File $file = null;

    #[ORM\Column(type: 'string')]
    private ?string $filename;

    #[ORM\Column(type: 'string')]
    private ?string $path;

    #[ORM\Column(type: 'integer')]
    private ?int $size;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getMessage(): ChatMessage {
        return $this->message;
    }

    public function setMessage(ChatMessage $message): ChatMessageAttachment {
        $this->message = $message;
        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file = null): ChatMessageAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }

    public function setFilename(?string $filename): ChatMessageAttachment {
        $this->filename = $filename;
        return $this;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(?string $path): ChatMessageAttachment {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
        return $this->size;
    }

    public function setSize(?int $size): ChatMessageAttachment {
        $this->size = $size;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
}
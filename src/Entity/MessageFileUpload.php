<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity]
class MessageFileUpload {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: MessageFile::class, inversedBy: 'uploads')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MessageFile $messageFile = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[Vich\UploadableField(mapping: 'messages', fileNameProperty: 'path', size: 'size', originalName: 'filename')]
    private $file;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $path = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updatedAt = null;

    public function getMessageFile(): MessageFile {
        return $this->messageFile;
    }

    public function setMessageFile(MessageFile $messageFile): MessageFileUpload {
        $this->messageFile = $messageFile;
        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): MessageFileUpload {
        $this->user = $user;
        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    /**
     * @return MessageFileUpload
     */
    public function setFile(?File $file = null) {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }

    public function setFilename(?string $filename): MessageFileUpload {
        $this->filename = $filename;
        return $this;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(?string $path): MessageFileUpload {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
        return $this->size;
    }

    public function setSize(?int $size): MessageFileUpload {
        $this->size = $size;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isUploaded() {
        return $this->getFilename() !== null && $this->getPath() !== null && $this->getSize() !== null;
    }
}
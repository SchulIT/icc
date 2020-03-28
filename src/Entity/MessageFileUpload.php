<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 */
class MessageFileUpload {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="MessageFile", inversedBy="uploads")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var MessageFile
     */
    private $messageFile;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    private $user;

    /**
     * @Vich\UploadableField(mapping="messages", fileNameProperty="path", originalName="filename", size="size")
     */
    private $file;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $filename;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return MessageFile
     */
    public function getMessageFile(): MessageFile {
        return $this->messageFile;
    }

    /**
     * @param MessageFile $messageFile
     * @return MessageFileUpload
     */
    public function setMessageFile(MessageFile $messageFile): MessageFileUpload {
        $this->messageFile = $messageFile;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return MessageFileUpload
     */
    public function setUser(User $user): MessageFileUpload {
        $this->user = $user;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return MessageFileUpload
     */
    public function setFile(?File $file = null) {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return MessageFileUpload
     */
    public function setFilename(?string $filename): MessageFileUpload {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return MessageFileUpload
     */
    public function setPath(?string $path): MessageFileUpload {
        $this->path = $path;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return MessageFileUpload
     */
    public function setSize(?int $size): MessageFileUpload {
        $this->size = $size;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isUploaded() {
        return $this->getFilename() !== null && $this->getPath() !== null && $this->getSize() !== null;
    }
}
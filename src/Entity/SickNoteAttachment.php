<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Auditable()
 * @Vich\Uploadable()
 */
class SickNoteAttachment {
    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="SickNote", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var SickNote|null
     */
    private $sickNote;

    /**
     * @Assert\File(maxSize="5M", mimeTypes={"application/pdf", "image/png", "image/jpg", "image/jpeg"})
     * @Vich\UploadableField(mapping="sick_notes", fileNameProperty="path", originalName="filename", size="size")
     */
    private $file;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $path;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeImmutable
     */
    private $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return SickNote|null
     */
    public function getSickNote(): ?SickNote {
        return $this->sickNote;
    }

    /**
     * @param SickNote $sickNote
     * @return SickNoteAttachment
     */
    public function setSickNote(SickNote $sickNote): SickNoteAttachment {
        $this->sickNote = $sickNote;
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
     * @return SickNoteAttachment
     */
    public function setFile(?File $file = null): SickNoteAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new \DateTimeImmutable();
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
     * @return SickNoteAttachment
     */
    public function setFilename(?string $filename): SickNoteAttachment {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): ?string {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return SickNoteAttachment
     */
    public function setPath(?string $path): SickNoteAttachment {
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
     * @return SickNoteAttachment
     */
    public function setSize(?int $size): SickNoteAttachment {
        $this->size = $size;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable {
        return $this->updatedAt;
    }
}
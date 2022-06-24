<?php

namespace App\Entity;

use App\Validator\Color;
use DateTime;
use DateTimeImmutable;
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
class DocumentAttachment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Document
     */
    private $document;

    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="path", originalName="filename", size="size")
     */
    private $file;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
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
     * @var DateTime
     */
    private $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Document|null
     */
    public function getDocument(): ?Document {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return DocumentAttachment
     */
    public function setDocument(Document $document): DocumentAttachment {
        $this->document = $document;
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
     * @return DocumentAttachment
     */
    public function setFile(?File $file = null): DocumentAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
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
     * @return DocumentAttachment
     */
    public function setFilename(?string $filename): DocumentAttachment {
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
     * @return DocumentAttachment
     */
    public function setPath(?string $path): DocumentAttachment {
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
     * @return DocumentAttachment
     */
    public function setSize(?int $size): DocumentAttachment {
        $this->size = $size;
        return $this;
    }
}
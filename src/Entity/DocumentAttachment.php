<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[Auditable]
#[ORM\Entity]
class DocumentAttachment {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Document::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Document $document = null;

    #[Vich\UploadableField(mapping: 'documents', fileNameProperty: 'path', size: 'size', originalName: 'filename')]
    private $file;

    #[Assert\NotNull]
    #[ORM\Column(type: 'text')]
    private ?string $filename = null;

    #[ORM\Column(type: 'string')]
    private ?string $path = null;

    #[ORM\Column(type: 'integer')]
    private ?int $size = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updatedAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getDocument(): ?Document {
        return $this->document;
    }

    public function setDocument(Document $document): DocumentAttachment {
        $this->document = $document;
        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file = null): DocumentAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }

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

    public function setPath(?string $path): DocumentAttachment {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
        return $this->size;
    }

    public function setSize(?int $size): DocumentAttachment {
        $this->size = $size;
        return $this;
    }
}
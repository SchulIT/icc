<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[Vich\Uploadable]
#[Auditable]
#[ORM\Entity]
class StudentAbsenceAttachment {

    use IdTrait;
    use UuidTrait;

    /**
     * @var StudentAbsence|null
     */
    #[ORM\ManyToOne(targetEntity: StudentAbsence::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?StudentAbsence $absence = null;

    #[Vich\UploadableField(mapping: 'student_absence', fileNameProperty: 'path', size: 'size', originalName: 'filename')]
    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'image/png', 'image/jpg', 'image/jpeg'])]
    private ?File $file = null;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $filename;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private string $path;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $size;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getAbsence(): ?StudentAbsence {
        return $this->absence;
    }

    public function setAbsence(StudentAbsence $absence): StudentAbsenceAttachment {
        $this->absence = $absence;
        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file = null): StudentAbsenceAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getFilename(): ?string {
        if(!isset($this->filename)) {
            return null;
        }

        return $this->filename;
    }

    public function setFilename(?string $filename): StudentAbsenceAttachment {
        $this->filename = $filename;
        return $this;
    }

    public function getPath(): ?string {
        if(!isset($this->path)) {
            return null;
        }

        return $this->path;
    }

    public function setPath(?string $path): StudentAbsenceAttachment {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
        if(!isset($this->size)) {
            return null;
        }

        return $this->size;
    }

    public function setSize(?int $size): StudentAbsenceAttachment {
        $this->size = $size;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
}
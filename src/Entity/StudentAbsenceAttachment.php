<?php

namespace App\Entity;

use DateTime;
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
class StudentAbsenceAttachment {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="StudentAbsence", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudentAbsence|null
     */
    private ?StudentAbsence $absence = null;

    /**
     * @Vich\UploadableField(mapping="student_absence", fileNameProperty="path", originalName="filename", size="size")
     */
    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'image/png', 'image/jpg', 'image/jpeg'])]
    private ?File $file = null;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    #[Assert\NotBlank]
    private string $filename;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $path;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $size;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
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
        return $this->filename;
    }

    public function setFilename(?string $filename): StudentAbsenceAttachment {
        $this->filename = $filename;
        return $this;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(?string $path): StudentAbsenceAttachment {
        $this->path = $path;
        return $this;
    }

    public function getSize(): ?int {
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
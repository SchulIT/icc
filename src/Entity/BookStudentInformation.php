<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class BookStudentInformation {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Student $student;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    private ?string $content;

    #[ORM\Column(name: '`from`', type: 'date')]
    private DateTime $from;

    #[ORM\Column(type: 'date')]
    #[Assert\GreaterThan(propertyPath: 'from')]
    private DateTime $until;

    #[ORM\Column(type: 'boolean')]
    private bool $includeInGradeBookExport = false;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): BookStudentInformation {
        $this->student = $student;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): BookStudentInformation {
        $this->content = $content;
        return $this;
    }

    public function getFrom(): DateTime {
        return $this->from;
    }

    public function setFrom(DateTime $from): BookStudentInformation {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): DateTime {
        return $this->until;
    }

    public function setUntil(DateTime $until): BookStudentInformation {
        $this->until = $until;
        return $this;
    }

    public function isIncludeInGradeBookExport(): bool {
        return $this->includeInGradeBookExport;
    }

    public function setIncludeInGradeBookExport(bool $includeInGradeBookExport): BookStudentInformation {
        $this->includeInGradeBookExport = $includeInGradeBookExport;
        return $this;
    }
}
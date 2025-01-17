<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class StudentAbsenceMessage {

    use IdTrait;
    use UuidTrait;

    /**
     * @var StudentAbsence|null
     */
    #[ORM\ManyToOne(targetEntity: StudentAbsence::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?StudentAbsence $absence = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    #[Encrypted]
    private ?string $message = null;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $createdBy = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $createdAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }

    public function setAbsence(StudentAbsence $absence): StudentAbsenceMessage {
        $this->absence = $absence;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): StudentAbsenceMessage {
        $this->message = $message;
        return $this;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }
}
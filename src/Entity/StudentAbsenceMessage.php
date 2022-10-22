<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class StudentAbsenceMessage {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="StudentAbsence", inversedBy="messages")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudentAbsence|null
     */
    private ?StudentAbsence $absence = null;

    /**
     * @ORM\Column(type="text")
     * @var string|null
     */
    #[Assert\NotBlank]
    private ?string $message = null;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User|null
     */
    private ?User $createdBy = null;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime|null
     */
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
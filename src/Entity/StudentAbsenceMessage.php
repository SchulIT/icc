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
     * @var StudentAbsence
     */
    private StudentAbsence $absence;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string|null
     */
    private ?string $message;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @var User
     */
    private User $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime|null
     */
    private ?DateTime $createdAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return StudentAbsence
     */
    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }

    /**
     * @param StudentAbsence $absence
     * @return StudentAbsenceMessage
     */
    public function setAbsence(StudentAbsence $absence): StudentAbsenceMessage {
        $this->absence = $absence;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return StudentAbsenceMessage
     */
    public function setMessage(?string $message): StudentAbsenceMessage {
        $this->message = $message;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }
}
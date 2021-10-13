<?php

namespace App\Entity;

use App\Entity\SickNoteReason;
use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonNotInPast;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class SickNote {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Student|null
     */
    private $student;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @Assert\NotNull()
     * @DateLessonNotInPast(exceptions={"ROLE_SICK_NOTE_CREATOR"})
     * @var DateLesson|null
     */
    private $from;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonGreaterThan(propertyPath="from")
     * @Assert\NotNull()
     * @var DateLesson|null
     */
    private $until;

    /**
     * @ORM\Column(type="sick_reason")
     * @Assert\NotNull()
     * @var SickNoteReason|null
     */
    private $reason;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"quarantine"})
     * @var string|null
     */
    private $orderedBy;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $message;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @var User
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime|null
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="SickNoteAttachment", mappedBy="sickNote", cascade={"persist"})
     * @ORM\OrderBy({"filename"="asc"})
     * @var Collection<SickNoteAttachment>
     */
    private $attachments;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->attachments = new ArrayCollection();
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return SickNote
     */
    public function setStudent(?Student $student): SickNote {
        $this->student = $student;
        return $this;
    }

    /**
     * @return DateLesson|null
     */
    public function getFrom(): ?DateLesson {
        return $this->from;
    }

    /**
     * @param DateLesson|null $from
     * @return SickNote
     */
    public function setFrom(?DateLesson $from): SickNote {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateLesson|null
     */
    public function getUntil(): ?DateLesson {
        return $this->until;
    }

    /**
     * @param DateLesson|null $until
     * @return SickNote
     */
    public function setUntil(?DateLesson $until): SickNote {
        $this->until = $until;
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

    public function addAttachment(SickNoteAttachment $attachment): void {
        if($attachment->getSickNote() === $this) {
            // Do not read already existing attachments (seems to fix a bug with VichUploaderBundle https://github.com/dustin10/VichUploaderBundle/issues/842)
            return;
        }

        $attachment->setSickNote($this);
        $this->attachments->add($attachment);
    }

    public function removeAttachment(SickNoteAttachment $attachment): void {
        $this->attachments->removeElement($attachment);
    }

    public function getAttachments(): Collection {
        return $this->attachments;
    }

    /**
     * @return SickNoteReason|null
     */
    public function getReason(): ?SickNoteReason {
        return $this->reason;
    }

    /**
     * @param SickNoteReason|null $reason
     * @return SickNote
     */
    public function setReason(?SickNoteReason $reason): SickNote {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return SickNote
     */
    public function setEmail(?string $email): SickNote {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return SickNote
     */
    public function setPhone(?string $phone): SickNote {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderedBy(): ?string {
        return $this->orderedBy;
    }

    /**
     * @param string|null $orderedBy
     * @return SickNote
     */
    public function setOrderedBy(?string $orderedBy): SickNote {
        $this->orderedBy = $orderedBy;
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
     * @return SickNote
     */
    public function setMessage(?string $message): SickNote {
        $this->message = $message;
        return $this;
    }
}
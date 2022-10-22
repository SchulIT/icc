<?php

namespace App\Entity;

use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonNotInPast;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class StudentAbsence {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Student|null
     */
    private ?Student $student;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @Assert\NotNull()
     * @DateLessonNotInPast(exceptions={"ROLE_STUDENT_ABSENCE_CREATOR"})
     * @var DateLesson|null
     */
    private ?DateLesson $from;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonGreaterThan(propertyPath="from")
     * @Assert\NotNull()
     * @var DateLesson|null
     */
    private ?DateLesson $until;

    /**
     * @ORM\ManyToOne(targetEntity="StudentAbsenceType")
     * @ORM\JoinColumn()
     * @Assert\NotNull()
     */
    private ?StudentAbsenceType $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Assert\Email()
     * @var string|null
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @var string|null
     */
    private ?string $message;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User|null
     */
    private ?User $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime|null
     */
    private ?DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var User|null
     */
    private ?User $approvedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private ?DateTime $approvedAt;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isApproved = false;

    /**
     * @ORM\OneToMany(targetEntity="StudentAbsenceAttachment", mappedBy="absence", cascade={"persist"})
     * @ORM\OrderBy({"filename"="asc"})
     * @var Collection<StudentAbsenceAttachment>
     */
    private Collection $attachments;

    /**
     * @ORM\OneToMany(targetEntity="StudentAbsenceMessage", mappedBy="absence", cascade={"persist"})
     * @var Collection<StudentAbsenceMessage>
     */
    private Collection $messages;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->attachments = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return StudentAbsence
     */
    public function setStudent(?Student $student): StudentAbsence {
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
     * @return StudentAbsence
     */
    public function setFrom(?DateLesson $from): StudentAbsence {
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
     * @return StudentAbsence
     */
    public function setUntil(?DateLesson $until): StudentAbsence {
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

    public function addAttachment(StudentAbsenceAttachment $attachment): void {
        if($attachment->getAbsence() === $this) {
            // Do not read already existing attachments (seems to fix a bug with VichUploaderBundle https://github.com/dustin10/VichUploaderBundle/issues/842)
            return;
        }

        $attachment->setAbsence($this);
        $this->attachments->add($attachment);
    }

    public function removeAttachment(StudentAbsenceAttachment $attachment): void {
        $this->attachments->removeElement($attachment);
    }

    public function getAttachments(): Collection {
        return $this->attachments;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return StudentAbsence
     */
    public function setEmail(?string $email): StudentAbsence {
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
     * @return StudentAbsence
     */
    public function setPhone(?string $phone): StudentAbsence {
        $this->phone = $phone;
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
     * @return StudentAbsence
     */
    public function setMessage(?string $message): StudentAbsence {
        $this->message = $message;
        return $this;
    }

    /**
     * @return StudentAbsenceType
     */
    public function getType(): StudentAbsenceType {
        return $this->type;
    }

    /**
     * @param StudentAbsenceType $type
     * @return StudentAbsence
     */
    public function setType(StudentAbsenceType $type): StudentAbsence {
        $this->type = $type;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getApprovedBy(): ?User {
        return $this->approvedBy;
    }

    /**
     * @param User|null $approvedBy
     * @return StudentAbsence
     */
    public function setApprovedBy(?User $approvedBy): StudentAbsence {
        $this->approvedBy = $approvedBy;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getApprovedAt(): ?DateTime {
        return $this->approvedAt;
    }

    /**
     * @param DateTime|null $approvedAt
     * @return StudentAbsence
     */
    public function setApprovedAt(?DateTime $approvedAt): StudentAbsence {
        $this->approvedAt = $approvedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool {
        return $this->isApproved;
    }

    /**
     * @param bool $isApproved
     * @return StudentAbsence
     */
    public function setIsApproved(bool $isApproved): StudentAbsence {
        $this->isApproved = $isApproved;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getMessages(): Collection {
        return $this->messages;
    }

    public function addMessage(StudentAbsenceMessage $message): void {
        $this->messages->add($message);
    }

    public function removeMessage(StudentAbsenceMessage $message): void {
        $this->messages->removeElement($message);
    }
}
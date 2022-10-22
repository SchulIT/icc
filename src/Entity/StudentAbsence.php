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
     * @var Student|null
     */
    #[Assert\NotNull]
    private ?Student $student = null;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonNotInPast(exceptions={"ROLE_STUDENT_ABSENCE_CREATOR"})
     * @var DateLesson|null
     */
    #[Assert\NotNull]
    private ?DateLesson $from = null;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonGreaterThan(propertyPath="from")
     * @var DateLesson|null
     */
    #[Assert\NotNull]
    private ?DateLesson $until = null;

    /**
     * @ORM\ManyToOne(targetEntity="StudentAbsenceType")
     * @ORM\JoinColumn()
     */
    #[Assert\NotNull]
    private ?StudentAbsenceType $type = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $phone = null;

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

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var User|null
     */
    private ?User $approvedBy = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private ?DateTime $approvedAt = null;

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

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): StudentAbsence {
        $this->student = $student;
        return $this;
    }

    public function getFrom(): ?DateLesson {
        return $this->from;
    }

    public function setFrom(?DateLesson $from): StudentAbsence {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): ?DateLesson {
        return $this->until;
    }

    public function setUntil(?DateLesson $until): StudentAbsence {
        $this->until = $until;
        return $this;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

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

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): StudentAbsence {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): StudentAbsence {
        $this->phone = $phone;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): StudentAbsence {
        $this->message = $message;
        return $this;
    }

    public function getType(): ?StudentAbsenceType {
        return $this->type;
    }

    public function setType(?StudentAbsenceType $type): StudentAbsence {
        $this->type = $type;
        return $this;
    }

    public function getApprovedBy(): ?User {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): StudentAbsence {
        $this->approvedBy = $approvedBy;
        return $this;
    }

    public function getApprovedAt(): ?DateTime {
        return $this->approvedAt;
    }

    public function setApprovedAt(?DateTime $approvedAt): StudentAbsence {
        $this->approvedAt = $approvedAt;
        return $this;
    }

    public function isApproved(): bool {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): StudentAbsence {
        $this->isApproved = $isApproved;
        return $this;
    }

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
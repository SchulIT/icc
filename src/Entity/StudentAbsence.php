<?php

namespace App\Entity;

use App\Validator\DateLessonGreaterThan;
use App\Validator\DateLessonInSection;
use App\Validator\DateLessonNotChanged;
use App\Validator\DateLessonNotInPast;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class StudentAbsence {

    public const int MaxNumberOfAttachments = 3;

    use IdTrait;
    use UuidTrait;

    /**
     * @var Student|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Student $student = null;

    /**
     * @var DateLesson|null
     */
    #[DateLessonNotInPast(exceptions: ['ROLE_STUDENT_ABSENCE_CREATOR'], propertyName: 'from')]
    #[DateLessonInSection]
    #[Assert\NotNull]
    #[ORM\Embedded(class: DateLesson::class)]
    private ?DateLesson $from = null;

    /**
     * @var DateLesson|null
     */
    #[DateLessonGreaterThan(propertyPath: 'from')]
    #[DateLessonInSection]
    #[Assert\NotNull]
    #[ORM\Embedded(class: DateLesson::class)]
    private ?DateLesson $until = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StudentAbsenceType::class)]
    #[ORM\JoinColumn]
    private ?StudentAbsenceType $type = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phone = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $createdBy = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $createdAt = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $approvedBy = null;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $approvedAt = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isApproved = false;

    /**
     * @var Collection<StudentAbsenceAttachment>
     */
    #[ORM\OneToMany(mappedBy: 'absence', targetEntity: StudentAbsenceAttachment::class, cascade: ['persist'])]
    #[ORM\OrderBy(['filename' => 'asc'])]
    #[Assert\Count(max: self::MaxNumberOfAttachments)]
    private Collection $attachments;

    /**
     * @var Collection<StudentAbsenceMessage>
     */
    #[ORM\OneToMany(mappedBy: 'absence', targetEntity: StudentAbsenceMessage::class, cascade: ['persist'])]
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

    /**
     * @return Collection<StudentAbsenceAttachment>
     */
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
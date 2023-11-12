<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class BookIntegrityCheckViolation {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', unique: true, nullable: false)]
    private ?string $referenceId = null;

    #[ORM\Column(type: 'date')]
    private DateTime $date;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: TimetableLesson::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?TimetableLesson $lesson;

    #[ORM\Column(type: 'integer')]
    private int $lessonNumber;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\Column(type: 'boolean')]
    private bool $isSuppressed = false;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getReferenceId(): ?string {
        return $this->referenceId;
    }

    public function setReferenceId(?string $referenceId): BookIntegrityCheckViolation {
        $this->referenceId = $referenceId;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): BookIntegrityCheckViolation {
        $this->date = $date;
        return $this;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): BookIntegrityCheckViolation {
        $this->student = $student;
        return $this;
    }

    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    public function setLesson(?TimetableLesson $lesson): BookIntegrityCheckViolation {
        $this->lesson = $lesson;
        return $this;
    }

    public function getLessonNumber(): int {
        return $this->lessonNumber;
    }

    public function setLessonNumber(int $lessonNumber): BookIntegrityCheckViolation {
        $this->lessonNumber = $lessonNumber;
        return $this;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): BookIntegrityCheckViolation {
        $this->message = $message;
        return $this;
    }

    public function isSuppressed(): bool {
        return $this->isSuppressed;
    }

    public function setIsSuppressed(bool $isSuppressed): BookIntegrityCheckViolation {
        $this->isSuppressed = $isSuppressed;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }

}
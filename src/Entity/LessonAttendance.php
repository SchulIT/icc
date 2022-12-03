<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class LessonAttendance implements JsonSerializable, Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'integer')]
    private int $type = LessonAttendanceType::Absent;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: LessonEntry::class, inversedBy: 'attendances')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?LessonEntry $entry = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn]
    private ?Student $student = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(type: 'integer')]
    private int $lateMinutes = 0;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(type: 'integer')]
    private ?int $absentLessons = 0;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[Assert\Choice(choices: [0, 1, 2])]
    #[ORM\Column(type: 'integer')]
    private int $excuseStatus = LessonAttendanceExcuseStatus::NotSet;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getType(): int {
        return $this->type;
    }

    public function setType(int $type): LessonAttendance {
        $this->type = $type;

        return $this;
    }

    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    public function setEntry(?LessonEntry $entry): LessonAttendance {
        $this->entry = $entry;
        return $this;
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): LessonAttendance {
        $this->student = $student;
        return $this;
    }

    public function getLateMinutes(): int {
        return $this->lateMinutes;
    }

    public function setLateMinutes(int $lateMinutes): LessonAttendance {
        $this->lateMinutes = $lateMinutes;
        return $this;
    }

    public function getAbsentLessons(): ?int {
        return $this->absentLessons;
    }

    public function setAbsentLessons(?int $absentLessons): LessonAttendance {
        $this->absentLessons = $absentLessons;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): LessonAttendance {
        $this->comment = $comment;
        return $this;
    }

    public function getExcuseStatus(): int {
        return $this->excuseStatus;
    }

    public function setExcuseStatus(int $excuseStatus): LessonAttendance {
        $this->excuseStatus = $excuseStatus;
        return $this;
    }

    public function jsonSerialize(): array {
        return [
            'uuid' => $this->getUuid()->toString(),
            'type' => $this->getType(),
            'student' => $this->getStudent(),
            'minutes' => $this->getLateMinutes(),
            'lessons' => $this->getAbsentLessons(),
            'excuse_status' => $this->getExcuseStatus(),
            'comment' => $this->getComment()
        ];
    }

    public function __toString(): string {
        return (string)$this->getStudent();
    }
}
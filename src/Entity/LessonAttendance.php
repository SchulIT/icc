<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class LessonAttendance implements JsonSerializable, Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="integer")
     */
    private int $type = LessonAttendanceType::Absent;

    /**
     * @ORM\ManyToOne(targetEntity="LessonEntry", inversedBy="attendances")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?LessonEntry $entry = null;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn()
     */
    #[Assert\NotNull]
    private ?Student $student = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThanOrEqual(0)]
    private int $lateMinutes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $absentLessons = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $comment = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\Choice(choices: [0, 1, 2])]
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

    /**
     * @return mixed
     */
    public function jsonSerialize() {
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
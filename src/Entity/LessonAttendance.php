<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class LessonAttendance implements JsonSerializable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $type = LessonAttendanceType::Absent;

    /**
     * @ORM\ManyToOne(targetEntity="LessonEntry", inversedBy="attendances")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var LessonEntry|null
     */
    private $entry;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn()
     * @Assert\NotNull()
     * @var Student
     */
    private $student;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $lateMinutes = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $absentLessons = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $comment = null;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices={0, 1, 2})
     * @var int
     */
    private $excuseStatus = LessonAttendanceExcuseStatus::NotSet;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @param int $type
     * @return LessonAttendance
     */
    public function setType(int $type): LessonAttendance {
        $this->type = $type;

        return $this;
    }

    /**
     * @return LessonEntry|null
     */
    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    /**
     * @param LessonEntry|null $entry
     * @return LessonAttendance
     */
    public function setEntry(?LessonEntry $entry): LessonAttendance {
        $this->entry = $entry;
        return $this;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return LessonAttendance
     */
    public function setStudent(Student $student): LessonAttendance {
        $this->student = $student;
        return $this;
    }

    /**
     * @return int
     */
    public function getLateMinutes(): int {
        return $this->lateMinutes;
    }

    /**
     * @param int $lateMinutes
     * @return LessonAttendance
     */
    public function setLateMinutes(int $lateMinutes): LessonAttendance {
        $this->lateMinutes = $lateMinutes;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAbsentLessons(): ?int {
        return $this->absentLessons;
    }

    /**
     * @param int|null $absentLessons
     * @return LessonAttendance
     */
    public function setAbsentLessons(?int $absentLessons): LessonAttendance {
        $this->absentLessons = $absentLessons;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return LessonAttendance
     */
    public function setComment(?string $comment): LessonAttendance {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return int
     */
    public function getExcuseStatus(): int {
        return $this->excuseStatus;
    }

    /**
     * @param int $excuseStatus
     * @return LessonAttendance
     */
    public function setExcuseStatus(int $excuseStatus): LessonAttendance {
        $this->excuseStatus = $excuseStatus;
        return $this;
    }

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
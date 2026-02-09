<?php

namespace App\Entity;

use App\Form\LessonEntryType;
use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class Attendance implements JsonSerializable, Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'integer', enumType: AttendanceType::class)]
    private AttendanceType $type = AttendanceType::Present;

    #[ORM\ManyToOne(targetEntity: LessonEntry::class, inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?LessonEntry $entry = null;

    #[ORM\ManyToOne(targetEntity: BookEvent::class, inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?BookEvent $event = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(0)]
    private int $lesson = 0;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn]
    private ?Student $student = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(type: 'integer')]
    private int $lateMinutes = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isZeroAbsentLesson = false;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'integer', enumType: AttendanceExcuseStatus::class)]
    private AttendanceExcuseStatus $excuseStatus = AttendanceExcuseStatus::NotSet;

    /**
     * @var Collection<ExcuseNote>
     */
    #[ORM\ManyToMany(targetEntity: ExcuseNote::class, inversedBy: 'associatedAttendances')]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $associatedExcuses;

    /**
     * @var Collection<AttendanceFlag>
     */
    #[ORM\ManyToMany(targetEntity: AttendanceFlag::class)]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $flags;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->flags = new ArrayCollection();
        $this->associatedExcuses = new ArrayCollection();
    }

    public function getType(): AttendanceType {
        return $this->type;
    }

    public function setType(AttendanceType $type): Attendance {
        $this->type = $type;

        return $this;
    }

    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    public function setEntry(?LessonEntry $entry): Attendance {
        $this->entry = $entry;
        return $this;
    }

    public function getEvent(): ?BookEvent {
        return $this->event;
    }

    public function setEvent(?BookEvent $event): Attendance {
        $this->event = $event;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): Attendance {
        $this->lesson = $lesson;
        return $this;
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): Attendance {
        $this->student = $student;
        return $this;
    }

    public function getLateMinutes(): int {
        return $this->lateMinutes;
    }

    public function setLateMinutes(int $lateMinutes): Attendance {
        $this->lateMinutes = $lateMinutes;
        return $this;
    }

    public function isZeroAbsentLesson(): bool {
        return $this->isZeroAbsentLesson;
    }

    public function setIsZeroAbsentLesson(bool $isZeroAbsentLesson): Attendance {
        $this->isZeroAbsentLesson = $isZeroAbsentLesson;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): Attendance {
        $this->comment = $comment;
        return $this;
    }

    public function getExcuseStatus(): AttendanceExcuseStatus {
        return $this->excuseStatus;
    }

    public function setExcuseStatus(AttendanceExcuseStatus $excuseStatus): Attendance {
        $this->excuseStatus = $excuseStatus;
        return $this;
    }

    public function getAssociatedExcuses(): Collection {
        return $this->associatedExcuses;
    }

    public function addAssociatedExcuse(ExcuseNote $excuseNote): void {
        $this->associatedExcuses->add($excuseNote);
    }

    public function removeAssociatedExcuse(ExcuseNote $excuseNote): void {
        $this->associatedExcuses->removeElement($excuseNote);
    }

    public function isExcused(): bool {
        if($this->type !== AttendanceType::Absent) {
            return true;
        }

        if($this->isZeroAbsentLesson()) {
            return true;
        }

        if($this->excuseStatus === AttendanceExcuseStatus::Excused) {
            return true;
        }

        return $this->associatedExcuses->count() > 0;
    }

    public function addFlag(AttendanceFlag $flag): void {
        $this->flags->add($flag);
    }

    public function removeFlag(AttendanceFlag $flag): void {
        $this->flags->removeElement($flag);
    }

    /**
     * @return Collection<AttendanceFlag>
     */
    public function getFlags(): Collection {
        return $this->flags;
    }

    public function getDate(): ?DateTime {
        return $this->getEntry()?->getLesson()->getDate() ?? $this->getEvent()?->getDate();
    }

    public function jsonSerialize(): array {
        return [
            'uuid' => $this->getUuid()->toString(),
            'type' => $this->getType()->value,
            'student' => $this->getStudent(),
            'minutes' => $this->getLateMinutes(),
            'is_zero_absent_lesson' => $this->isZeroAbsentLesson(),
            'excuse_status' => $this->getExcuseStatus()->value,
            'comment' => $this->getComment(),
            'flags' => $this->flags->map(fn(AttendanceFlag $flag) => $flag->getId())->toArray(),
            'lesson' => $this->getLesson(),
            'has_excuses' => $this->associatedExcuses->count() > 0,
        ];
    }

    public function __toString(): string {
        return (string)$this->getStudent();
    }
}
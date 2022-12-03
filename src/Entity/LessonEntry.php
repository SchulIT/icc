<?php

namespace App\Entity;

use App\Validator\UniqueLessonEntry;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueLessonEntry(groups: ['Default', 'cancel'])]
#[ORM\Entity]
class LessonEntry {

    use IdTrait;
    use UuidTrait;

    /**
     * @var TimetableLesson|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: TimetableLesson::class, inversedBy: 'entries')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?TimetableLesson $lesson = null;

    /**
     * @var int
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lesson.lessonStart', groups: ['Default', 'cancel'])]
    #[ORM\Column(type: 'integer')]
    private int $lessonStart = 1;

    /**
     * @var int
     */
    #[Assert\LessThanOrEqual(propertyPath: 'lesson.lessonEnd', groups: ['Default', 'cancel'])]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart', groups: ['Default', 'cancel'])]
    #[ORM\Column(type: 'integer')]
    private int $lessonEnd = 1;

    /**
     * @var Tuition|null
     */
    #[Assert\NotNull(groups: ['Default', 'cancel'])]
    #[ORM\ManyToOne(targetEntity: Tuition::class)]
    #[ORM\JoinColumn]
    private ?Tuition $tuition = null;

    /**
     * @var Subject|null
     */
    #[Assert\NotNull(groups: ['Default', 'cancel'])]
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn]
    private ?Subject $subject = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $replacementSubject = null;

    /**
     * @var Teacher|null
     */
    #[Assert\NotNull(groups: ['Default', 'cancel'])]
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn]
    private ?Teacher $teacher = null;

    /**
     * @var Teacher|null
     */
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn]
    private ?Teacher $replacementTeacher = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(groups: ['Default'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $topic = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true, groups: ['Default'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $exercises = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true, groups: ['Default'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<LessonAttendance>
     */
    #[Assert\Valid(groups: ['Default'])]
    #[ORM\OneToMany(mappedBy: 'entry', targetEntity: LessonAttendance::class, cascade: ['persist'])]
    private Collection $attendances;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isCancelled = false;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(groups: ['cancel'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $cancelReason = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->attendances = new ArrayCollection();
    }

    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    public function setLesson(?TimetableLesson $lesson): LessonEntry {
        $this->lesson = $lesson;
        return $this;
    }

    public function getCancelReason(): ?string {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): LessonEntry {
        $this->cancelReason = $cancelReason;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): LessonEntry {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): LessonEntry {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    public function setTuition(?Tuition $tuition): LessonEntry {
        $this->tuition = $tuition;
        return $this;
    }

    public function getSubject(): ?Subject {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): LessonEntry {
        $this->subject = $subject;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): LessonEntry {
        $this->teacher = $teacher;
        return $this;
    }

    public function getTopic(): ?string {
        return $this->topic;
    }

    public function setTopic(?string $topic): LessonEntry {
        $this->topic = $topic;
        return $this;
    }

    public function getExercises(): ?string {
        return $this->exercises;
    }

    public function setExercises(?string $exercises): LessonEntry {
        $this->exercises = $exercises;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): LessonEntry {
        $this->comment = $comment;
        return $this;
    }

    public function addAttendance(LessonAttendance $attendance): void {
        $attendance->setEntry($this);
        $this->attendances->add($attendance);
    }

    public function removeAttendance(LessonAttendance $attendance): void {
        $this->attendances->removeElement($attendance);
    }

    public function getAttendances(): Collection {
        return $this->attendances;
    }

    public function isCancelled(): bool {
        return $this->isCancelled;
    }

    public function setIsCancelled(bool $isCancelled): LessonEntry {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    public function setReplacementSubject(?string $replacementSubject): LessonEntry {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    public function getReplacementTeacher(): ?Teacher {
        return $this->replacementTeacher;
    }

    public function setReplacementTeacher(?Teacher $replacementTeacher): LessonEntry {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }
}
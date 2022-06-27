<?php

namespace App\Entity;

use App\Validator\UniqueLessonEntry;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueLessonEntry(groups={"Default", "cancel"})
 */
class LessonEntry {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="TimetableLesson", inversedBy="entries")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var TimetableLesson|null
     */
    private ?TimetableLesson $lesson;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="lesson.lessonStart", groups={"Default", "cancel"})
     * @var int
     */
    private int $lessonStart = 1;

    /**
     * @ORM\Column(type="integer")
     * @Assert\LessThanOrEqual(propertyPath="lesson.lessonEnd", groups={"Default", "cancel"})
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart", groups={"Default", "cancel"})
     * @var int
     */
    private int $lessonEnd = 1;

    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn()
     * @Assert\NotNull(groups={"Default", "cancel"})
     * @var Tuition|null
     */
    private ?Tuition $tuition;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn()
     * @Assert\NotNull(groups={"Default", "cancel"})
     * @var Subject|null
     */
    private ?Subject $subject;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private ?string $replacementSubject;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn()
     * @Assert\NotNull(groups={"Default", "cancel"})
     * @var Teacher|null
     */
    private ?Teacher $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn()
     * @var Teacher|null
     */
    private ?Teacher $replacementTeacher;

    /**
     * @ORM\Column(type="string", nullable=true);
     * @Assert\NotBlank(groups={"Default"})
     * @var string|null
     */
    private ?string $topic;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=true, groups={"Default"})
     * @var string|null
     */
    private ?string $exercises;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=true, groups={"Default"})
     * @var string|null
     */
    private ?string $comment;

    /**
     * @ORM\OneToMany(targetEntity="LessonAttendance", mappedBy="entry", cascade={"persist"})
     * @Assert\Valid(groups={"Default"})
     * @var Collection<LessonAttendance>
     */
    private Collection $attendances;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private bool $isCancelled = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"cancel"})
     * @var string|null
     */
    private ?string $cancelReason;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->attendances = new ArrayCollection();
    }

    /**
     * @return TimetableLesson|null
     */
    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    /**
     * @param TimetableLesson|null $lesson
     * @return LessonEntry
     */
    public function setLesson(?TimetableLesson $lesson): LessonEntry {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCancelReason(): ?string {
        return $this->cancelReason;
    }

    /**
     * @param string|null $cancelReason
     * @return LessonEntry
     */
    public function setCancelReason(?string $cancelReason): LessonEntry {
        $this->cancelReason = $cancelReason;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @param int $lessonStart
     * @return LessonEntry
     */
    public function setLessonStart(int $lessonStart): LessonEntry {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @param int $lessonEnd
     * @return LessonEntry
     */
    public function setLessonEnd(int $lessonEnd): LessonEntry {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Tuition|null
     */
    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition|null $tuition
     * @return LessonEntry
     */
    public function setTuition(?Tuition $tuition): LessonEntry {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return Subject|null
     */
    public function getSubject(): ?Subject {
        return $this->subject;
    }

    /**
     * @param Subject|null $subject
     * @return LessonEntry
     */
    public function setSubject(?Subject $subject): LessonEntry {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return LessonEntry
     */
    public function setTeacher(?Teacher $teacher): LessonEntry {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTopic(): ?string {
        return $this->topic;
    }

    /**
     * @param string|null $topic
     * @return LessonEntry
     */
    public function setTopic(?string $topic): LessonEntry {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExercises(): ?string {
        return $this->exercises;
    }

    /**
     * @param string|null $exercises
     * @return LessonEntry
     */
    public function setExercises(?string $exercises): LessonEntry {
        $this->exercises = $exercises;
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
     * @return LessonEntry
     */
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

    /**
     * @return Collection
     */
    public function getAttendances(): Collection {
        return $this->attendances;
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool {
        return $this->isCancelled;
    }

    /**
     * @param bool $isCancelled
     * @return LessonEntry
     */
    public function setIsCancelled(bool $isCancelled): LessonEntry {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReplacementSubject(): ?string {
        return $this->replacementSubject;
    }

    /**
     * @param string|null $replacementSubject
     * @return LessonEntry
     */
    public function setReplacementSubject(?string $replacementSubject): LessonEntry {
        $this->replacementSubject = $replacementSubject;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getReplacementTeacher(): ?Teacher {
        return $this->replacementTeacher;
    }

    /**
     * @param Teacher|null $replacementTeacher
     * @return LessonEntry
     */
    public function setReplacementTeacher(?Teacher $replacementTeacher): LessonEntry {
        $this->replacementTeacher = $replacementTeacher;
        return $this;
    }
}
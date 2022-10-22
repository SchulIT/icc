<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class TimetableLesson {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string|null
     */
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="date")
     * @var DateTime|null
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $lessonStart;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private int $lessonEnd;

    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true, onDelete="SET NULL")
     * @var Tuition|null
     */
    private ?Tuition $tuition = null;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private ?Room $room = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private ?string $location = null;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Subject|null
     */
    private ?Subject $subject = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $subjectName = null;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="timetable_lesson_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private Collection $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="timetable_lesson_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Grade>
     */
    private Collection $grades;

    /**
     * @ORM\OneToMany(targetEntity="LessonEntry", mappedBy="lesson", cascade={"persist"})
     * @var Collection<LessonEntry>
     */
    private $entries;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): TimetableLesson {
        $this->externalId = $externalId;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): TimetableLesson {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): TimetableLesson {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): TimetableLesson {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    public function setTuition(?Tuition $tuition): TimetableLesson {
        $this->tuition = $tuition;
        return $this;
    }

    public function getRoom(): ?Room {
        return $this->room;
    }

    public function setRoom(?Room $room): TimetableLesson {
        $this->room = $room;
        return $this;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(?string $location): TimetableLesson {
        $this->location = $location;
        return $this;
    }

    public function getSubject(): ?Subject {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): TimetableLesson {
        $this->subject = $subject;
        return $this;
    }

    public function getSubjectName(): ?string {
        return $this->subjectName;
    }

    public function setSubjectName(?string $subjectName): TimetableLesson {
        $this->subjectName = $subjectName;
        return $this;
    }

    public function addTeacher(Teacher $teacher): void {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(Teacher $teacher): void {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function addGrade(Grade $grade): void {
        $this->grades->add($grade);
    }

    public function removeGrade(Grade $grade): void {
        $this->grades->removeElement($grade);
    }

    /**
     * @return Collection<Grade>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    /**
     * @return Collection<LessonEntry>
     */
    public function getEntries(): Collection {
        return $this->entries;
    }
}
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
    private ?string $externalId;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private ?DateTime $date;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $lessonStart;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int
     */
    private int $lessonEnd;

    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true, onDelete="SET NULL")
     * @var Tuition|null
     */
    private ?Tuition $tuition;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private ?Room $room;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private ?string $location;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Subject|null
     */
    private ?Subject $subject;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private ?string $subjectName;

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

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return TimetableLesson
     */
    public function setExternalId(string $externalId): TimetableLesson {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return TimetableLesson
     */
    public function setDate(?DateTime $date): TimetableLesson {
        $this->date = $date;
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
     * @return TimetableLesson
     */
    public function setLessonStart(int $lessonStart): TimetableLesson {
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
     * @return TimetableLesson
     */
    public function setLessonEnd(int $lessonEnd): TimetableLesson {
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
     * @return TimetableLesson
     */
    public function setTuition(?Tuition $tuition): TimetableLesson {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room {
        return $this->room;
    }

    /**
     * @param Room|null $room
     * @return TimetableLesson
     */
    public function setRoom(?Room $room): TimetableLesson {
        $this->room = $room;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return TimetableLesson
     */
    public function setLocation(?string $location): TimetableLesson {
        $this->location = $location;
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
     * @return TimetableLesson
     */
    public function setSubject(?Subject $subject): TimetableLesson {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubjectName(): ?string {
        return $this->subjectName;
    }

    /**
     * @param string|null $subjectName
     * @return TimetableLesson
     */
    public function setSubjectName(?string $subjectName): TimetableLesson {
        $this->subjectName = $subjectName;
        return $this;
    }

    /**
     * @param Teacher $teacher
     */
    public function addTeacher(Teacher $teacher): void {
        $this->teachers->add($teacher);
    }

    /**
     * @param Teacher $teacher
     */
    public function removeTeacher(Teacher $teacher): void {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    /**
     * @param Grade $grade
     */
    public function addGrade(Grade $grade): void {
        $this->grades->add($grade);
    }

    /**
     * @param Grade $grade
     */
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
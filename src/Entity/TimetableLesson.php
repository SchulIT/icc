<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class TimetableLesson {

    use IdTrait;
    use UuidTrait;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $externalId = null;

    /**
     * @var DateTime|null
     */
    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $lessonStart;

    /**
     * @var int
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    #[ORM\Column(type: 'integer')]
    private int $lessonEnd;

    /**
     * @var Tuition|null
     */
    #[ORM\ManyToOne(targetEntity: Tuition::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Tuition $tuition = null;

    /**
     * @var Room|null
     */
    #[ORM\ManyToOne(targetEntity: Room::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Room $room = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $location = null;

    /**
     * @var Subject|null
     */
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Subject $subject = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subjectName = null;

    /**
     * @var Collection<Teacher>
     */
    #[ORM\JoinTable(name: 'timetable_lesson_teachers')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Teacher::class)]
    private Collection $teachers;

    /**
     * @var Collection<Grade>
     */
    #[ORM\JoinTable(name: 'timetable_lesson_grades')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Grade::class)]
    private Collection $grades;

    /**
     * @var Collection<LessonEntry>
     */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: LessonEntry::class, cascade: ['persist'])]
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
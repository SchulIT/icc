<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @ORM\Table(uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"external_id", "period_id"})
 * })
 */
class TimetableLesson {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity="TimetablePeriod", inversedBy="lessons")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var TimetablePeriod|null
     */
    private $period;

    /**
     * @ORM\ManyToOne(targetEntity="TimetableWeek")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var TimetableWeek|null
     */
    private $week;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="1", max="7")
     * @var int
     */
    private $day = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lesson = 0;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isDoubleLesson = false;

    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true, onDelete="SET NULL")
     * @var Tuition|null
     */
    private $tuition;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private $room;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Subject|null
     */
    private $subject;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $subjectName;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="timetable_lesson_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="timetable_lesson_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Grade>
     */
    private $grades;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
        $this->grades = new ArrayCollection();
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
     * @return TimetablePeriod|null
     */
    public function getPeriod(): ?TimetablePeriod {
        return $this->period;
    }

    /**
     * @param TimetablePeriod|null $period
     * @return TimetableLesson
     */
    public function setPeriod(?TimetablePeriod $period): TimetableLesson {
        $this->period = $period;
        return $this;
    }

    /**
     * @return TimetableWeek|null
     */
    public function getWeek(): ?TimetableWeek {
        return $this->week;
    }

    /**
     * @param TimetableWeek|null $week
     * @return TimetableLesson
     */
    public function setWeek(?TimetableWeek $week): TimetableLesson {
        $this->week = $week;
        return $this;
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @param int $day
     * @return TimetableLesson
     */
    public function setDay(int $day): TimetableLesson {
        $this->day = $day;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return TimetableLesson
     */
    public function setLesson(int $lesson): TimetableLesson {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDoubleLesson(): bool {
        return $this->isDoubleLesson;
    }

    /**
     * @param bool $isDoubleLesson
     * @return TimetableLesson
     */
    public function setIsDoubleLesson(bool $isDoubleLesson): TimetableLesson {
        $this->isDoubleLesson = $isDoubleLesson;
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
}
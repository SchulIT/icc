<?php

namespace App\Entity;

use App\Validator\DateInActiveSection;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class BookEvent {
    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'date')]
    #[DateInActiveSection]
    #[Assert\NotNull]
    private DateTime $date;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(0)]
    private int $lessonStart;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private int $lessonEnd;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Teacher|null $teacher;

    /**
     * @var Collection<Attendance>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Attendance::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $attendances;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->grades = new ArrayCollection();
        $this->attendances = new ArrayCollection();
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): BookEvent {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): BookEvent {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): BookEvent {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): BookEvent {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): BookEvent {
        $this->description = $description;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): BookEvent {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Collection<Attendance>
     */
    public function getAttendances(): Collection {
        return $this->attendances;
    }

    public function addAttendance(Attendance $attendance): void {

        $this->attendances->add($attendance);
    }

    public function removeAttendance(Attendance $attendance): void {
        $this->attendances->removeElement($attendance);
    }
}
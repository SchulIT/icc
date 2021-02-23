<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class TimetableSupervision {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity="TimetablePeriod", inversedBy="supervisions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @ORM\ManyToMany(targetEntity="Week")
     * @ORM\JoinTable(
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Week>
     */
    private $weeks;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="1", max="7")
     * @var int
     */
    private $day;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $lesson;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isBefore = true;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $location;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->weeks = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return TimetableSupervision
     */
    public function setExternalId(string $externalId): TimetableSupervision {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return TimetablePeriod
     */
    public function getPeriod(): TimetablePeriod {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return TimetableSupervision
     */
    public function setPeriod(TimetablePeriod $period): TimetableSupervision {
        $this->period = $period;
        return $this;
    }

    public function addWeek(Week $week): void {
        $this->weeks->add($week);
    }

    public function removeWeek(Week $week): void {
        $this->weeks->removeElement($week);
    }

    public function getWeeks(): Collection {
        return $this->weeks;
    }

    public function getWeeksAsIntArray(): array {
        return $this->weeks->map(function(Week $week) {
            return $week->getNumber();
        })->toArray();
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @param int $day
     * @return TimetableSupervision
     */
    public function setDay(int $day): TimetableSupervision {
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
     * @return TimetableSupervision
     */
    public function setLesson(int $lesson): TimetableSupervision {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBefore(): bool {
        return $this->isBefore;
    }

    /**
     * @param bool $isBefore
     * @return TimetableSupervision
     */
    public function setIsBefore(bool $isBefore): TimetableSupervision {
        $this->isBefore = $isBefore;
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return TimetableSupervision
     */
    public function setTeacher(Teacher $teacher): TimetableSupervision {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @param string $location
     * @return TimetableSupervision
     */
    public function setLocation(string $location): TimetableSupervision {
        $this->location = $location;
        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class TimetableSupervision {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

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
     * @ORM\ManyToOne(targetEntity="TimetableWeek")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var TimetableWeek
     */
    private $week;

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
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $location;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
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

    /**
     * @return TimetableWeek
     */
    public function getWeek(): TimetableWeek {
        return $this->week;
    }

    /**
     * @param TimetableWeek $week
     * @return TimetableSupervision
     */
    public function setWeek(TimetableWeek $week): TimetableSupervision {
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

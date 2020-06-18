<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="`type`", type="string")
 * @ORM\DiscriminatorMap({
 *      "tuition" = "TuitionTimetableLesson",
 *      "freestyle" = "FreestyleTimetableLesson"
 * })
 */
abstract class TimetableLesson {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
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

    public function __construct() {
        $this->uuid = Uuid::uuid4();

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
}
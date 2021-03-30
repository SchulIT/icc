<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Week {

    use IdTrait;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @var int
     */
    private $number = 0;

    /**
     * @ORM\ManyToOne(targetEntity="TimetableWeek", inversedBy="weeks")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var TimetableWeek|null
     */
    private $timetableWeek;

    /**
     * @Assert\GreaterThan(0)
     * @return int
     */
    public function getNumber(): int {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Week
     */
    public function setNumber(int $number): Week {
        $this->number = $number;
        return $this;
    }

    /**
     * @return TimetableWeek|null
     */
    public function getTimetableWeek(): ?TimetableWeek {
        return $this->timetableWeek;
    }

    /**
     * @param TimetableWeek|null $timetableWeek
     * @return Week
     */
    public function setTimetableWeek(?TimetableWeek $timetableWeek): Week {
        $this->timetableWeek = $timetableWeek;
        return $this;
    }
}
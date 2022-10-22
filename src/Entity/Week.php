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
     */
    private int $number = 0;

    /**
     * @ORM\ManyToOne(targetEntity="TimetableWeek", inversedBy="weeks")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?TimetableWeek $timetableWeek = null;

    #[Assert\GreaterThan(0)]
    public function getNumber(): int {
        return $this->number;
    }

    public function setNumber(int $number): Week {
        $this->number = $number;
        return $this;
    }

    public function getTimetableWeek(): ?TimetableWeek {
        return $this->timetableWeek;
    }

    public function setTimetableWeek(?TimetableWeek $timetableWeek): Week {
        $this->timetableWeek = $timetableWeek;
        return $this;
    }
}
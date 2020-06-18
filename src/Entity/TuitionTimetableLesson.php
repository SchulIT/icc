<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class TuitionTimetableLesson extends TimetableLesson {
    /**
     * @ORM\ManyToOne(targetEntity="Tuition")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Tuition
     */
    private $tuition;

    /**
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Room|null
     */
    private $room;

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition $tuition
     * @return TimetableLesson
     */
    public function setTuition(Tuition $tuition): TimetableLesson {
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
}
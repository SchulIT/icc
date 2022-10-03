<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 */
class DateLesson {
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     * @var int|null
     */
    private $lesson;

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return DateLesson
     */
    public function setDate(?DateTime $date): DateLesson {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLesson(): ?int {
        return $this->lesson;
    }

    /**
     * @param int|null $lesson
     * @return DateLesson
     */
    public function setLesson(?int $lesson): DateLesson {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * Note: boundaries are considered inclusive
     *
     * @param DateLesson $start
     * @param DateLesson $end
     * @return bool
     */
    public function isBetween(DateLesson $start, DateLesson $end): bool {
        if ($this->getDate() < $start->getDate() || $this->getDate() > $end->getDate()) {
            return false;
        }

        if ($start->getDate() == $this->getDate()) {
            return $start->getLesson() <= $this->getLesson();
        }

        if ($end->getDate() == $this->getDate()) {
            return $end->getLesson() >= $this->getLesson();
        }

        return true;
    }
}
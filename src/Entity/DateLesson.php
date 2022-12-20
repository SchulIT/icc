<?php

namespace App\Entity;

use App\Validator\DateInActiveSection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class DateLesson {
    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    #[DateInActiveSection]
    private ?DateTime $date = null;

    #[Assert\NotNull]
    #[Assert\NotNull]
    #[ORM\Column(type: 'integer')]
    private ?int $lesson = null;

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): DateLesson {
        $this->date = $date;
        return $this;
    }

    public function getLesson(): ?int {
        return $this->lesson;
    }

    public function setLesson(?int $lesson): DateLesson {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * Note: boundaries are considered inclusive
     */
    public function isBetween(DateLesson $start, DateLesson $end): bool {
        if ($this->getDate() < $start->getDate() || $this->getDate() > $end->getDate()) {
            return false;
        }

        if($start->getDate() == $this->getDate() && $end->getDate() == $this->getDate()) {
            return $start->getLesson() <= $this->getLesson()
                && $this->getLesson() <= $end->getLesson();
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
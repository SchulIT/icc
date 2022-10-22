<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class FreeTimespan {

    use IdTrait;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotNull]
    private ?\DateTime $date = null;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThan(0)]
    private int $start = 1;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    private int $end = 1;

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): FreeTimespan {
        $this->date = $date;
        return $this;
    }

    public function getStart(): int {
        return $this->start;
    }

    public function setStart(int $start): FreeTimespan {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): int {
        return $this->end;
    }

    public function setEnd(int $end): FreeTimespan {
        $this->end = $end;
        return $this;
    }
}
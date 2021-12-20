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
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $start = 1;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(propertyPath="start")
     * @var int
     */
    private $end = 1;

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return FreeTimespan
     */
    public function setDate(?DateTime $date): FreeTimespan {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int {
        return $this->start;
    }

    /**
     * @param int $start
     * @return FreeTimespan
     */
    public function setStart(int $start): FreeTimespan {
        $this->start = $start;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int {
        return $this->end;
    }

    /**
     * @param int $end
     * @return FreeTimespan
     */
    public function setEnd(int $end): FreeTimespan {
        $this->end = $end;
        return $this;
    }
}
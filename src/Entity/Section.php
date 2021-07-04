<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"number", "year"})
 * })
 * @UniqueEntity(fields={"number", "year"})
 */
class Section {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $number = 1;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $year;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $displayName;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $start;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @Assert\GreaterThan(propertyPath="start")
     * @var DateTime|null
     */
    private $end;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return int
     */
    public function getNumber(): int {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Section
     */
    public function setNumber(int $number): Section {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Section
     */
    public function setYear(int $year): Section {
        $this->year = $year;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return Section
     */
    public function setDisplayName(?string $displayName): Section {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStart(): ?DateTime {
        return $this->start;
    }

    /**
     * @param DateTime|null $start
     * @return Section
     */
    public function setStart(?DateTime $start): Section {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEnd(): ?DateTime {
        return $this->end;
    }

    /**
     * @param DateTime|null $end
     * @return Section
     */
    public function setEnd(?DateTime $end): Section {
        $this->end = $end;
        return $this;
    }
}
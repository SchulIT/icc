<?php

namespace App\Entity;

use Stringable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(columns: ['number', 'year'])]
class Section implements Stringable
{
    use IdTrait;
    use UuidTrait;
    #[ORM\Column(type: 'integer')]
    private int $number = 1;
    #[ORM\Column(type: 'integer')]
    private ?int $year = null;
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private ?string $displayName = null;
    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $start = null;
    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    #[ORM\Column(type: 'date')]
    private ?DateTime $end = null;
    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }
    public function getNumber(): int {
        return $this->number;
    }
    public function setNumber(int $number): Section {
        $this->number = $number;
        return $this;
    }
    public function getYear(): int {
        return $this->year;
    }
    public function setYear(int $year): Section {
        $this->year = $year;
        return $this;
    }
    public function getDisplayName(): ?string {
        return $this->displayName;
    }
    public function setDisplayName(?string $displayName): Section {
        $this->displayName = $displayName;
        return $this;
    }
    public function getStart(): ?DateTime {
        return $this->start;
    }
    public function setStart(?DateTime $start): Section {
        $this->start = $start;
        return $this;
    }
    public function getEnd(): ?DateTime {
        return $this->end;
    }
    public function setEnd(?DateTime $end): Section {
        $this->end = $end;
        return $this;
    }
    public function __toString(): string {
        return (string) $this->displayName;
    }
}
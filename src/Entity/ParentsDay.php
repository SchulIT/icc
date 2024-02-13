<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ParentsDay {
    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    private DateTime $date;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    private DateTime $bookingAllowedFrom;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(propertyPath: 'bookingAllowedFrom')]
    #[Assert\LessThan(propertyPath: 'date')]
    private DateTime $bookingAllowedUntil;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->grades = new ArrayCollection();
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): ParentsDay {
        $this->title = $title;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getBookingAllowedFrom(): DateTime {
        return $this->bookingAllowedFrom;
    }

    public function setBookingAllowedFrom(DateTime $bookingAllowedFrom): ParentsDay {
        $this->bookingAllowedFrom = $bookingAllowedFrom;
        return $this;
    }

    public function getBookingAllowedUntil(): DateTime {
        return $this->bookingAllowedUntil;
    }

    public function setBookingAllowedUntil(DateTime $bookingAllowedUntil): ParentsDay {
        $this->bookingAllowedUntil = $bookingAllowedUntil;
        return $this;
    }

    public function setDate(DateTime $date): ParentsDay {
        $this->date = $date;
        return $this;
    }
}
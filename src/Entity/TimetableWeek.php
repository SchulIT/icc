<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"key"})
 */
class TimetableWeek {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, name="`key`")
     * @Assert\NotBlank()
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $displayName;

    /**
     * @ORM\OneToMany(targetEntity="Week", mappedBy="timetableWeek", cascade={"persist", "remove", "refresh"})
     * @var Collection<Week>
     */
    private $weeks;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->weeks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param string $key
     * @return TimetableWeek
     */
    public function setKey($key): TimetableWeek {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName() {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return TimetableWeek
     */
    public function setDisplayName($displayName): TimetableWeek {
        $this->displayName = $displayName;
        return $this;
    }

    public function addWeek(Week $week): void {
        $week->setTimetableWeek($this);
        $this->weeks->add($week);
    }

    public function removeWeek(Week $week): void {
        $week->setTimetableWeek(null);
        $this->weeks->removeElement($week);
    }

    public function getWeeks(): Collection {
        return $this->weeks;
    }

    public function getWeeksAsIntArray(): array {
        return $this->weeks->map(function(Week $week) {
            return $week->getNumber();
        })->toArray();
    }

    public function __toString() {
        return $this->displayName;
    }
}
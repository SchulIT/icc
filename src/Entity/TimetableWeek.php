<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueEntity(fields: ['key'])]
#[ORM\Entity]
class TimetableWeek implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(name: '`key`', type: 'string', unique: true)]
    private string $key;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private string $displayName;

    /**
     * @var Collection<Week>
     */
    #[ORM\OneToMany(mappedBy: 'timetableWeek', targetEntity: Week::class, cascade: ['persist', 'remove', 'refresh'])]
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
        return $this->weeks->map(fn(Week $week) => $week->getNumber())->toArray();
    }

    public function __toString(): string {
        return $this->displayName;
    }
}
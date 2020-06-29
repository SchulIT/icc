<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
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
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $weekMod;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
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

    /**
     * @return int
     */
    public function getWeekMod() {
        return $this->weekMod;
    }

    /**
     * @param int $weekMod
     * @return TimetableWeek
     */
    public function setWeekMod($weekMod): TimetableWeek {
        $this->weekMod = $weekMod;
        return $this;
    }

    public function __toString() {
        return $this->displayName;
    }
}
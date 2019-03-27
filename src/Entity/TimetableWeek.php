<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"key"})
 */
class TimetableWeek {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $displayName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     * @var int
     */
    private $weekMod;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param string $key
     * @return TimetableWeek
     */
    public function setKey(string $key): TimetableWeek {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return TimetableWeek
     */
    public function setDisplayName(string $displayName): TimetableWeek {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeekMod(): int {
        return $this->weekMod;
    }

    /**
     * @param int $weekMod
     * @return TimetableWeek
     */
    public function setWeekMod(int $weekMod): TimetableWeek {
        $this->weekMod = $weekMod;
        return $this;
    }
}
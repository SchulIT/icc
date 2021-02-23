<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Week {

    use IdTrait;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @var int
     */
    private $number = 0;

    /**
     * @Assert\GreaterThan(0)
     * @return int
     */
    public function getNumber(): int {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Week
     */
    public function setNumber(int $number): Week {
        $this->number = $number;
        return $this;
    }
}
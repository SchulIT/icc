<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Infotext {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @var string|null
     */
    private $content;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return Infotext
     */
    public function setDate(?DateTime $date) {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Infotext
     */
    public function setContent(string $content) {
        $this->content = $content;
        return $this;
    }
}
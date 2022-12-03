<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class Infotext {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @return Infotext
     */
    public function setDate(?DateTime $date) {
        $this->date = $date;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @return Infotext
     */
    public function setContent(string $content) {
        $this->content = $content;
        return $this;
    }
}
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }
}
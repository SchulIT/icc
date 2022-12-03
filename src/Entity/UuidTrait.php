<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait {

    /**
     * @var UuidInterface
     */
    #[ORM\Column(type: 'uuid')]
    private $uuid;

    public function getUuid(): UuidInterface {
        return $this->uuid;
    }
}
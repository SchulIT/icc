<?php

namespace App\Common\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    public function getId(): ?int {
        return $this->id;
    }
}
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait {

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private $id;

    public function getId(): ?int {
        return $this->id;
    }
}
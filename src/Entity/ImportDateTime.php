<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ImportDateTime {

    use IdTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $entityClass = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updatedAt = null;

    public function getEntityClass(): string {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): ImportDateTime {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): ImportDateTime {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
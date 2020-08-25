<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ImportDateTime {

    use IdTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    private $entityClass;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @return string
     */
    public function getEntityClass(): string {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return ImportDateTime
     */
    public function setEntityClass(string $entityClass): ImportDateTime {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return ImportDateTime
     */
    public function setUpdatedAt(DateTime $updatedAt): ImportDateTime {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
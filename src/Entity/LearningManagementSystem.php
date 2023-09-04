<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class LearningManagementSystem {

    use IdTrait;
    use UuidTrait;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $externalId;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return LearningManagementSystem
     */
    public function setExternalId(?string $externalId): LearningManagementSystem {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return LearningManagementSystem
     */
    public function setName(?string $name): LearningManagementSystem {
        $this->name = $name;
        return $this;
    }

    public function __toString(): string {
        return $this->getName();
    }
}
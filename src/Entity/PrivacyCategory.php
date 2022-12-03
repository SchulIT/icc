<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class PrivacyCategory implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private string $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): PrivacyCategory {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label): PrivacyCategory {
        $this->label = $label;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): PrivacyCategory {
        $this->description = $description;
        return $this;
    }

    public function __toString(): string {
        return $this->getLabel();
    }
}
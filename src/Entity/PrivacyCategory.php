<?php

namespace App\Entity;

use App\Validator\NullOrNotBlank;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class PrivacyCategory {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @NullOrNotBlank()
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $description;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return PrivacyCategory
     */
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
     * @return PrivacyCategory
     */
    public function setLabel($label): PrivacyCategory {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return PrivacyCategory
     */
    public function setDescription(?string $description): PrivacyCategory {
        $this->description = $description;
        return $this;
    }
}
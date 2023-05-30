<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoryData {

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $label = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Serializer\Type('string')]
    private ?string $description = null;

    public function setId(string $id): PrivacyCategoryData {
        $this->id = $id;
        return $this;
    }

    public function setLabel(string $label): PrivacyCategoryData {
        $this->label = $label;
        return $this;
    }

    public function setDescription(?string $description): PrivacyCategoryData {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    public function getDescription(): ?string {
        return $this->description;
    }
}
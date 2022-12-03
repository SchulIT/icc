<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoryData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
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

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }
}
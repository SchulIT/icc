<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoryData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $label;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $description;

    /**
     * @param string $id
     * @return PrivacyCategoryData
     */
    public function setId(string $id): PrivacyCategoryData {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $label
     * @return PrivacyCategoryData
     */
    public function setLabel(string $label): PrivacyCategoryData {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string|null $description
     * @return PrivacyCategoryData
     */
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
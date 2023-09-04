<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoryData {

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private string $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private string $name;

    /**
     * Hex color string without leading hashtag. Note: abbreviated values (e.g. 000, fff, ...) are not allowed.
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $color = null;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): AppointmentCategoryData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): AppointmentCategoryData {
        $this->name = $name;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): AppointmentCategoryData {
        $this->color = $color;
        return $this;
    }
}
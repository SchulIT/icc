<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoryData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $id;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $name;

    /**
     * Hex color string without leading hashtag. Note: abbreviated values (e.g. 000, fff, ...) are not allowed.
     *
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
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

    /**
     * @return string|null
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor($color): AppointmentCategoryData {
        $this->color = $color;
        return $this;
    }
}
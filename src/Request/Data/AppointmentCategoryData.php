<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoryData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("integer")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * Hex color string without leading hashtag. Note: abbreviated values (e.g. 000, fff, ...) are not allowed.
     *
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $color;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     * @return AppointmentCategoryData
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
     * @return AppointmentCategoryData
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
     * @return AppointmentCategoryData
     */
    public function setColor($color): AppointmentCategoryData {
        $this->color = $color;
        return $this;
    }
}
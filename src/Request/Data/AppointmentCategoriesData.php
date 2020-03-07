<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoriesData {

    /**
     * @Serializer\Type("array<App\Request\Data\AppointmentCategoryData>")
     * @Assert\Valid()
     * @var AppointmentCategoryData[]
     */
    private $categories;

    /**
     * @return mixed
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     * @return AppointmentCategoriesData
     */
    public function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }
}
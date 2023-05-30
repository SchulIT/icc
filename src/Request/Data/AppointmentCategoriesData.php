<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoriesData {

    /**
     * @var AppointmentCategoryData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\AppointmentCategoryData>')]
    private array $categories = [ ];

    /**
     * @return AppointmentCategoryData[]
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @param AppointmentCategoryData[] $categories
     * @return AppointmentCategoriesData
     */
    public function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }
}
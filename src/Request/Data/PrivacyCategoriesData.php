<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoriesData {

    /**
     * @Serializer\Type("array<App\Request\Data\PrivacyCategoryData>")
     * @var PrivacyCategoryData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    private array $categories = [ ];

    /**
     * @return PrivacyCategoryData[]
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @param PrivacyCategoryData[] $categories
     * @return $this
     */
    public function setCategories(array $categories): PrivacyCategoriesData {
        $this->categories = $categories;
        return $this;
    }
}
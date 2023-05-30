<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoriesData {

    /**
     * @var PrivacyCategoryData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\PrivacyCategoryData>')]
    private array $categories = [ ];

    /**
     * @return PrivacyCategoryData[]
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @param PrivacyCategoryData[] $categories
     */
    public function setCategories(array $categories): PrivacyCategoriesData {
        $this->categories = $categories;
        return $this;
    }
}
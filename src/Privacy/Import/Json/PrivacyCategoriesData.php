<?php

namespace App\Privacy\Import\Json;

use App\Framework\Validator\UniqueId;
use App\Privacy\Import\Json\PrivacyCategoryData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoriesData {

    /**
     * @var PrivacyCategoryData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<' . PrivacyCategoryData::class . '>')]
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
<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoriesData {

    /**
     * @Serializer\Type("array<App\Request\Data\PrivacyCategoryData>")
     * @UniqueId(propertyPath="id")
     * @var PrivacyCategoryData[]
     */
    #[Assert\Valid]
    private array $categories = [ ];

    /**
     * @return PrivacyCategoryData[]
     */
    public function getCategories() {
        return $this->categories;
    }
}
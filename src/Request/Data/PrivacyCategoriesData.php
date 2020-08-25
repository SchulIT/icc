<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PrivacyCategoriesData {

    /**
     * @Serializer\Type("array<App\Request\Data\PrivacyCategoryData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var PrivacyCategoryData[]
     */
    private $categories = [ ];

    /**
     * @return PrivacyCategoryData[]
     */
    public function getCategories() {
        return $this->categories;
    }
}
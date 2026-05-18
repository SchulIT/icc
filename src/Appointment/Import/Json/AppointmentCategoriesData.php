<?php

namespace App\Appointment\Import\Json;

use App\Framework\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentCategoriesData {

    /**
     * @var AppointmentCategoryData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<' . AppointmentCategoryData::class . '>')]
    private array $categories = [ ];

    /**
     * @return AppointmentCategoryData[]
     */
    public function getCategories(): array {
        return $this->categories;
    }

    /**
     * @param AppointmentCategoryData[] $categories
     * @return AppointmentCategoriesData
     */
    public function setCategories(array $categories): static {
        $this->categories = $categories;
        return $this;
    }
}
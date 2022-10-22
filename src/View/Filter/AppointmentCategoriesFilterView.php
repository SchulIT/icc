<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;

class AppointmentCategoriesFilterView implements FilterViewInterface {

    /**
     * @param AppointmentCategory[] $categories
     * @param AppointmentCategory[] $currentCategories
     */
    public function __construct(private array $categories, private array $currentCategories)
    {
    }

    /**
     * @return AppointmentCategory[]
     */
    public function getCategories(): array {
        return $this->categories;
    }

    /**
     * @return AppointmentCategory[]
     */
    public function getCurrentCategories(): array {
        return $this->currentCategories;
    }

    public function isEnabled(): bool {
        return count($this->categories) > 0;
    }
}
<?php

namespace App\Appointment\View\Filter;

use App\Appointment\Entity\AppointmentCategory;
use App\Framework\View\Filter\FilterViewInterface;

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
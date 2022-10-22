<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;

class AppointmentCategoryFilterView implements FilterViewInterface {

    /**
     * @param AppointmentCategory[] $categories
     */
    public function __construct(private array $categories, private ?AppointmentCategory $currentCategory)
    {
    }

    /**
     * @return AppointmentCategory[]
     */
    public function getCategories(): array {
        return $this->categories;
    }

    public function getCurrentCategory(): ?AppointmentCategory {
        return $this->currentCategory;
    }

    public function isEnabled(): bool {
        return count($this->categories) > 0;
    }
}
<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;

class AppointmentCategoriesFilterView {

    /** @var AppointmentCategory[] */
    private $categories;

    /** @var AppointmentCategory[] */
    private $currentCategories;

    public function __construct(array $categories, array $currentCategories) {
        $this->categories = $categories;
        $this->currentCategories = $currentCategories;
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
}
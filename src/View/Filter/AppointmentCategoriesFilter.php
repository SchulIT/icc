<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class AppointmentCategoriesFilter {
    public function __construct(private Sorter $sorter, private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository)
    {
    }

    public function handle(array $categoryUuids) {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->appointmentCategoryRepository->findAll(),
            fn(AppointmentCategory $category) => (string)$category->getUuid()
        );
        
        $selectedCategories = array_filter($categories, fn(AppointmentCategory $category) => in_array($category->getUuid(), $categoryUuids));

        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return new AppointmentCategoriesFilterView($categories, $selectedCategories);
    }
}
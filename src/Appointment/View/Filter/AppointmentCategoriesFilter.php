<?php

namespace App\Appointment\View\Filter;

use App\Appointment\Entity\AppointmentCategory;
use App\Appointment\Repository\AppointmentCategoryRepositoryInterface;
use App\Appointment\Sorting\AppointmentCategoryStrategy;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;
use App\Appointment\View\Filter\AppointmentCategoriesFilterView;

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
<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class AppointmentCategoryFilter {
    public function __construct(private Sorter $sorter, private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository)
    {
    }

    public function handle(?string $categoryUuid) {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->appointmentCategoryRepository->findAll(),
            fn(AppointmentCategory $category) => (string)$category->getUuid()
        );

        $category = $categoryUuid !== null ?
            $categories[$categoryUuid] ?? null : null;

        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return new AppointmentCategoryFilterView($categories, $category);
    }
}
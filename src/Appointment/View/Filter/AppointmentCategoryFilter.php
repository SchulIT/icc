<?php

namespace App\Appointment\View\Filter;

use App\Appointment\Entity\AppointmentCategory;
use App\Appointment\Repository\AppointmentCategoryRepositoryInterface;
use App\Appointment\Sorting\AppointmentCategoryStrategy;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;
use App\Appointment\View\Filter\AppointmentCategoryFilterView;

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
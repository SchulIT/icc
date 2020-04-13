<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class AppointmentCategoryFilter {
    private $sorter;
    private $appointmentCategoryRepository;

    public function __construct(Sorter $sorter, AppointmentCategoryRepositoryInterface $appointmentCategoryRepository) {
        $this->sorter = $sorter;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
    }

    public function handle(?string $categoryUuid) {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->appointmentCategoryRepository->findAll(),
            function (AppointmentCategory $category) {
                return (string)$category->getUuid();
            }
        );

        $category = $categoryUuid !== null ?
            $categories[$categoryUuid] ?? null : null;

        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return new AppointmentCategoryFilterView($categories, $category);
    }
}
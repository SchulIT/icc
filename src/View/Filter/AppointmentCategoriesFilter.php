<?php

namespace App\View\Filter;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class AppointmentCategoriesFilter {
    private $sorter;
    private $appointmentCategoryRepository;

    public function __construct(Sorter $sorter, AppointmentCategoryRepositoryInterface $appointmentCategoryRepository) {
        $this->sorter = $sorter;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
    }

    public function handle(array $categoryUuids) {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->appointmentCategoryRepository->findAll(),
            function (AppointmentCategory $category) {
                return (string)$category->getUuid();
            }
        );
        
        $selectedCategories = array_filter($categories, function (AppointmentCategory $category) use ($categoryUuids) {
            return in_array($category->getUuid(), $categoryUuids);
        });

        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return new AppointmentCategoriesFilterView($categories, $selectedCategories);
    }
}
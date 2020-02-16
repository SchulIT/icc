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

    public function handle(array $categoryIds) {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->appointmentCategoryRepository->findAll(),
            function (AppointmentCategory $category) {
                return $category->getId();
            }
        );

        dump($categoryIds);

        $selectedCategories = array_filter($categories, function (AppointmentCategory $category) use ($categoryIds) {
            return in_array($category->getId(), $categoryIds);
        });

        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return new AppointmentCategoriesFilterView($categories, $selectedCategories);
    }
}
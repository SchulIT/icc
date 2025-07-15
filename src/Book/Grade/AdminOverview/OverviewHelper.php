<?php

namespace App\Book\Grade\AdminOverview;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\TuitionGradeCategory;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TuitionStrategy;

readonly class OverviewHelper {

    public function __construct(private TuitionRepositoryInterface $tuitionRepository, private Sorter $sorter) {

    }

    /**
     * @param Grade[] $grades
     * @param TuitionGradeCategory[] $categories
     * @param Section $section
     * @return Overview
     */
    public function computeOverview(array $grades, array $categories, Section $section): Overview {
        $tuitions = $this->tuitionRepository->findAllByGrades($grades, $section);
        $this->sorter->sort($tuitions, TuitionStrategy::class);

        $rows = [ ];

        foreach ($tuitions as $tuition) {
            $enabledCategories = [ ];

            foreach($categories as $category) {
                if($category->getTuitions()->contains($tuition)) {
                    $enabledCategories[] = $category;
                }
            }

            $rows[] = new Row($tuition, $enabledCategories);
        }

        return new Overview($rows, $categories);
    }
}
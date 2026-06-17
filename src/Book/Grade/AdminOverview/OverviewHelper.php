<?php

namespace App\Book\Grade\AdminOverview;

use App\Book\Entity\TuitionGradeCategory;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Sorting\TuitionStrategy;
use App\Framework\Sorting\Sorter;

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
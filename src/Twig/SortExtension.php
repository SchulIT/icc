<?php

namespace App\Twig;

use App\Sorting\GradeTeacherStrategy;
use App\Sorting\Sorter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortExtension extends AbstractExtension {

    public function __construct(private Sorter $sorter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('sort_gradeteachers', [ $this, 'sortGradeTeachers'])
        ];
    }

    public function sortGradeTeachers(iterable $gradeTeachers): array {
        $collection = [ ];

        foreach($gradeTeachers as $gradeTeacher) {
            $collection[] = $gradeTeacher;
        }

        $this->sorter->sort($collection, GradeTeacherStrategy::class);

        return $collection;
    }
}
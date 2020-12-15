<?php

namespace App\Twig;

use App\Sorting\GradeTeacherStrategy;
use App\Sorting\Sorter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortExtension extends AbstractExtension {

    private $sorter;

    public function __construct(Sorter $sorter) {
        $this->sorter = $sorter;
    }

    public function getFilters() {
        return [
            new TwigFilter('sort_gradeteachers', [ $this, 'sortGradeTeachers'])
        ];
    }

    public function sortGradeTeachers(iterable $gradeTeachers) {
        $collection = [ ];

        foreach($gradeTeachers as $gradeTeacher) {
            $collection[] = $gradeTeacher;
        }

        $this->sorter->sort($collection, GradeTeacherStrategy::class);

        return $collection;
    }
}
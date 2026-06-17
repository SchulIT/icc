<?php

namespace App\Book\Grade;

use App\Book\Entity\TuitionGrade;
use App\Book\Entity\TuitionGradeCategory;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;

class GradeRow {

    public function __construct(private readonly Tuition|Student $tuitionOrStudent, /** @var TuitionGrade[] */ private readonly array $grades) { }

    public function getTuitionOrStudent(): Tuition|Student {
        return $this->tuitionOrStudent;
    }

    public function getGrade(Tuition $tuition, TuitionGradeCategory $category): ?TuitionGrade {
        return $this->grades[sprintf('%s_%s', $tuition->getUuid()->toString(), $category->getUuid()->toString())] ?? null;
    }
}
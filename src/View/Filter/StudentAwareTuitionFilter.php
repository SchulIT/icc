<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;

class StudentAwareTuitionFilter {
    public function __construct(private TuitionRepositoryInterface $repository, private Sorter $sorter)
    {
    }

    public function handle(?string $tuitionUuid, ?Section $section, ?Student $student): TuitionFilterView {
        if($section === null || $student === null) {
            return new TuitionFilterView([], null);
        }

        $tuitions = ArrayUtils::createArrayWithKeys(
            $this->repository->findAllByStudents([$student], $section),
            fn(Tuition $tuition) => $tuition->getUuid()->toString()
        );

        $tuition = $tuitionUuid !== null ?
            $tuitions[$tuitionUuid] ?? null : null;

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        return new TuitionFilterView($tuitions, $tuition);
    }
}
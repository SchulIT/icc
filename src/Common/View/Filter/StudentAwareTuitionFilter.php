<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\View\Filter\TuitionFilterView;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\TuitionStrategy;
use App\Framework\Utils\ArrayUtils;

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
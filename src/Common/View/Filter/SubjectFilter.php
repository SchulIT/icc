<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Subject;
use App\Framework\Grouping\Grouper;
use App\Common\Repository\SubjectRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\SubjectNameStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\SubjectFilterView;

class SubjectFilter {

    public function __construct(private Sorter $sorter, private SubjectRepositoryInterface $subjectRepository)
    {
    }

    public function handle(?string $subjectUuid, bool $onlySubjectsWithTeachers = true) {
        if($onlySubjectsWithTeachers === true) {
            $subjects = $this->subjectRepository->findAllWithTeachers();
        } else {
            $subjects = $this->subjectRepository->findAll();
        }

        $this->sorter->sort($subjects, SubjectNameStrategy::class);

        $subjects = ArrayUtils::createArrayWithKeys($subjects, fn(Subject $subject) => (string)$subject->getUuid());

        $subject = $subjectUuid !== null ?
            $subjects[$subjectUuid] ?? null : null;

        return new SubjectFilterView($subjects, $subject);
    }
}
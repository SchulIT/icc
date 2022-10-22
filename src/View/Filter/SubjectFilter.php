<?php

namespace App\View\Filter;

use App\Entity\Subject;
use App\Grouping\Grouper;
use App\Repository\SubjectRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\SubjectNameStrategy;
use App\Utils\ArrayUtils;

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
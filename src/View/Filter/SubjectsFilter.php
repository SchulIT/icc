<?php

namespace App\View\Filter;

use App\Repository\SubjectRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\SubjectNameStrategy;

class SubjectsFilter {
    private $sorter;
    private $subjectRepository;

    public function __construct(Sorter $sorter, SubjectRepositoryInterface $subjectRepository) {
        $this->sorter = $sorter;
        $this->subjectRepository = $subjectRepository;
    }

    public function handle(?array $subjectUuids) {
        if($subjectUuids === null) {
            $subjectUuids = [ ];
        }

        $subjects = $this->subjectRepository->findAll();
        $this->sorter->sort($subjects, SubjectNameStrategy::class);

        $currentSubjects = [ ];

        foreach($subjects as $subject) {
            if(in_array((string)$subject->getUuid(), $subjectUuids)) {
                $currentSubjects[] = $subject;
            }
        }

        return new SubjectsFilterView($subjects, $currentSubjects);
    }
}
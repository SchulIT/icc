<?php

namespace App\View\Filter;

use App\Entity\User;
use App\Entity\UserType;
use App\Repository\SubjectRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\SubjectNameStrategy;
use App\Utils\EnumArrayUtils;

class SubjectsFilter {
    private $sorter;
    private $subjectRepository;

    public function __construct(Sorter $sorter, SubjectRepositoryInterface $subjectRepository) {
        $this->sorter = $sorter;
        $this->subjectRepository = $subjectRepository;
    }

    public function handle(?array $subjectUuids, User $user) {
        if($subjectUuids === null) {
            $subjectUuids = [ ];
        }

        $subjects = [ ];

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent()]) === false) {
            $subjects = $this->subjectRepository->findAll();
        }
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
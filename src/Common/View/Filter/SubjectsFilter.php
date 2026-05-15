<?php

namespace App\Common\View\Filter;

use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Repository\SubjectRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\SubjectNameStrategy;
use App\Framework\Utils\EnumArrayUtils;
use App\Common\View\Filter\SubjectsFilterView;

class SubjectsFilter {
    public function __construct(private Sorter $sorter, private SubjectRepositoryInterface $subjectRepository)
    {
    }

    public function handle(?array $subjectUuids, User $user) {
        if($subjectUuids === null) {
            $subjectUuids = [ ];
        }

        $subjects = [ ];

        if($user->isStudentOrParent() === false) {
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
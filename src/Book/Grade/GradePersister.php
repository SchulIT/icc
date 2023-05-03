<?php

namespace App\Book\Grade;

use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\TuitionGradeVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GradePersister {
    public function __construct(private readonly TuitionGradeRepositoryInterface $repository, private readonly AuthorizationCheckerInterface $authorizationChecker) { }

    public function persist(Tuition|Student $tuitionOrStudent, GradeOverview $overview, array $grades): void {
        $this->repository->beginTransaction();

        foreach($overview->getRows() as $row) {
            foreach($overview->getCategories() as $category) {
                $subject = $row->getTuitionOrStudent();
                $grade = $row->getGrade($category->getTuition(), $category->getCategory());

                if($grade === null) {
                    $grade = (new TuitionGrade())
                        ->setCategory($category->getCategory());

                    if($tuitionOrStudent instanceof Tuition) {
                        $grade->setTuition($tuitionOrStudent);
                        $grade->setStudent($subject);
                    } else {
                        $grade->setStudent($tuitionOrStudent);
                        $grade->setTuition($subject);
                    }

                    if($this->authorizationChecker->isGranted(TuitionGradeVoter::New, $grade) !== true) {
                        continue;
                    }
                }

                if(isset($grades[$subject->getUuid()->toString()][$category->getCategory()->getUuid()->toString()][$category->getTuition()->getUuid()->toString()])) {
                    $encryptedGrade = $grades[$subject->getUuid()->toString()][$category->getCategory()->getUuid()->toString()][$category->getTuition()->getUuid()->toString()];

                    if(empty($encryptedGrade)) {
                        $grade->setEncryptedGrade(null);
                    } else {
                        $grade->setEncryptedGrade($encryptedGrade);
                    }
                }

                if(($grade->getId() === null && $this->authorizationChecker->isGranted(TuitionGradeVoter::New, $grade))
                   || ($grade->getId() !== null && $this->authorizationChecker->isGranted(TuitionGradeVoter::Edit, $grade))) {
                    $this->repository->persist($grade);
                }
            }
        }

        $this->repository->commit();
    }
}
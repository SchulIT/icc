<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;

class TuitionFilter {

    private $sorter;
    private $repository;

    public function __construct(TuitionRepositoryInterface $repository, Sorter $sorter) {
        $this->repository = $repository;
        $this->sorter = $sorter;
    }

    public function handle(?string $tuitionUuid, ?Section $section, User $user): TuitionFilterView {
        if($section === null) {
            return new TuitionFilterView([], null);
        }

        $keyFunc = function (Tuition $tuition) {
            return $tuition->getUuid()->toString();
        };

        if($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $tuitions = ArrayUtils::createArrayWithKeys(
                $this->repository->findAllByStudents($user->getStudents()->toArray(), $section),
                $keyFunc
            );
        } else {
            $tuitions = ArrayUtils::createArrayWithKeys(
                $this->repository->findAllBySection($section),
                $keyFunc
            );
        }

        $tuition = $tuitionUuid !== null ?
            $tuitions[$tuitionUuid] ?? null : null;

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        return new TuitionFilterView($tuitions, $tuition);
    }
}
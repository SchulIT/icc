<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Entity\Tuition;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\TuitionStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\TuitionFilterView;

class TuitionFilter {

    public function __construct(private TuitionRepositoryInterface $repository, private Sorter $sorter)
    {
    }

    public function handle(?string $tuitionUuid, ?Section $section, User $user, bool $onlyOwn = false): TuitionFilterView {
        if($section === null) {
            return new TuitionFilterView([], null);
        }

        $keyFunc = fn(Tuition $tuition) => $tuition->getUuid()->toString();

        if($user->isStudentOrParent()) {
            $tuitions = ArrayUtils::createArrayWithKeys(
                $this->repository->findAllByStudents($user->getStudents()->toArray(), $section),
                $keyFunc
            );
        } else if($user->isTeacher() && $onlyOwn) {
            $tuitions = ArrayUtils::createArrayWithKeys(
                $this->repository->findAllByTeacher($user->getTeacher(), $section),
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
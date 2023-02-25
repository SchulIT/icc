<?php

namespace App\View\Filter;

use App\Entity\LearningManagementSystem;
use App\Repository\LearningManagementSystemRepositoryInterface;
use App\Utils\ArrayUtils;

class LearningManagementSystemFilter {

    public function __construct(private readonly LearningManagementSystemRepositoryInterface $repository) { }

    public function handle(?string $lmsUuid): LearningManagementSystemFilterView {
        $lms = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(LearningManagementSystem $lms) => (string)$lms->getUuid()
        );

        $selected = $lmsUuid !== null ?
            $lms[$lmsUuid] ?? null : null;

        return new LearningManagementSystemFilterView($lms, $selected);
    }
}
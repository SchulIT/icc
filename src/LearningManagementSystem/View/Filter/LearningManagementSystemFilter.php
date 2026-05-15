<?php

namespace App\LearningManagementSystem\View\Filter;

use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\LearningManagementSystem\Repository\LearningManagementSystemRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use App\LearningManagementSystem\View\Filter\LearningManagementSystemFilterView;

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
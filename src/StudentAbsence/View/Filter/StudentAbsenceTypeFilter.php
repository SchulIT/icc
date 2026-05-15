<?php

namespace App\StudentAbsence\View\Filter;

use App\StudentAbsence\Entity\StudentAbsenceType;
use App\StudentAbsence\Repository\StudentAbsenceTypeRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use App\StudentAbsence\View\Filter\StudentAbsenceTypeFilterView;

class StudentAbsenceTypeFilter {

    public function __construct(private StudentAbsenceTypeRepositoryInterface $repository)
    {
    }

    public function handle(?string $typeUuid): StudentAbsenceTypeFilterView {
        $types = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(StudentAbsenceType $type) => (string)$type->getUuid()
        );

        $type = $typeUuid !== null ?
            $types[$typeUuid] ?? null : null;

        return new StudentAbsenceTypeFilterView($types, $type);
    }
}
<?php

namespace App\View\Filter;

use App\Entity\StudentAbsenceType;
use App\Repository\StudentAbsenceTypeRepositoryInterface;
use App\Utils\ArrayUtils;

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
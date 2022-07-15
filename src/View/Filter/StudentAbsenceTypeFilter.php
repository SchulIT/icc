<?php

namespace App\View\Filter;

use App\Entity\StudentAbsenceType;
use App\Repository\StudentAbsenceTypeRepositoryInterface;
use App\Utils\ArrayUtils;

class StudentAbsenceTypeFilter {

    private StudentAbsenceTypeRepositoryInterface $repository;

    public function __construct(StudentAbsenceTypeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function handle(?string $typeUuid): StudentAbsenceTypeFilterView {
        $types = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function (StudentAbsenceType $type) {
                return (string)$type->getUuid();
            }
        );

        $type = $typeUuid !== null ?
            $types[$typeUuid] ?? null : null;

        return new StudentAbsenceTypeFilterView($types, $type);
    }
}
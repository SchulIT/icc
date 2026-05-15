<?php

namespace App\Checklist\Repository;

use App\Checklist\Entity\Checklist;
use App\Common\Entity\Student;
use App\Common\Entity\User;

interface ChecklistRepositoryInterface {

    public function findAllByUser(User $user): array;

    public function persist(Checklist $checklist): void;

    public function remove(Checklist $checklist): void;
}
<?php

namespace App\Repository;

use App\Entity\Checklist;
use App\Entity\Student;
use App\Entity\User;

interface ChecklistRepositoryInterface {

    public function findAllByUser(User $user): array;

    public function persist(Checklist $checklist): void;

    public function remove(Checklist $checklist): void;
}
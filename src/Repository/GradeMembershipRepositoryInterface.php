<?php

namespace App\Repository;

use App\Entity\GradeMembership;
use App\Entity\Section;

interface GradeMembershipRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @param Section $section
     * @return GradeMembership[]
     */
    public function findAllBySection(Section $section): array;

    public function persist(GradeMembership $gradeMembership): void;

    public function removeAll(Section $section): void;
}
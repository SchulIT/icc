<?php

namespace App\Common\Repository;

use App\Common\Entity\GradeMembership;
use App\Common\Entity\Section;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface GradeMembershipRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @param Section $section
     * @return GradeMembership[]
     */
    public function findAllBySection(Section $section): array;

    public function persist(GradeMembership $gradeMembership): void;

    public function removeAll(Section $section): void;
}
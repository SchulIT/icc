<?php

namespace App\Common\Repository;

use App\Common\Entity\Section;
use App\Common\Entity\StudyGroupMembership;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface StudyGroupMembershipRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return StudyGroupMembership[]
     */
    public function findAll(): array;

    public function persist(StudyGroupMembership $membership): void;

    public function removeAll(Section $section): void;
}
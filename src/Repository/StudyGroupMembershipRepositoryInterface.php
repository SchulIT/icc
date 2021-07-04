<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\StudyGroupMembership;

interface StudyGroupMembershipRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return StudyGroupMembership[]
     */
    public function findAll(): array;

    public function persist(StudyGroupMembership $membership): void;

    public function removeAll(Section $section): void;
}
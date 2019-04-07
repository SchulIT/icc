<?php

namespace App\Repository;

use App\Entity\Subject;

interface SubjectRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Subject|null
     */
    public function findOneById(int $id): ?Subject;

    /**
     * @param string $abbreviation
     * @return Subject|null
     */
    public function findOneByAbbreviation(string $abbreviation): ?Subject;

    /**
     * @return Subject[]
     */
    public function findAll();

    /**
     * @param Subject $subject
     */
    public function persist(Subject $subject): void;

    /**
     * @param Subject $subject
     */
    public function remove(Subject $subject): void;
}
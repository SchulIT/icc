<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\SickNote;
use App\Entity\Student;
use App\Entity\User;
use DateTime;

interface SickNoteRepositoryInterface {
    /**
     * @param User $user
     * @return SickNote[]
     */
    public function findByUser(User $user): array;

    /**
     * Returns all sick notes for the given students
     *
     * @param Student[] $students
     * @return SickNote[]
     */
    public function findByStudents(array $students): array;

    /**
     * @param Grade $grade
     * @return SickNote[]
     */
    public function findByGrade(Grade $grade): array;

    /**
     * @return SickNote[]
     */
    public function findAll(): array;

    /**
     * @param DateTime $threshold
     * @return int Number of removed sick notes
     */
    public function removeExpired(DateTime $threshold): int;

    public function persist(SickNote $note): void;

    public function remove(SickNote $note): void;
}
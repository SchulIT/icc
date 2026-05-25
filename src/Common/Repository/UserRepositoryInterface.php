<?php

namespace App\Common\Repository;

use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use App\Message\Entity\Message;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Repository\TransactionalRepositoryInterface;

interface UserRepositoryInterface extends TransactionalRepositoryInterface {
    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?User;

    /**
     * @param string $uuid
     * @return User|null
     */
    public function findOneByUuid(string $uuid): ?User;

    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User;

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * @param Student[] $students
     * @return User[]
     */
    public function findAllParentsByStudents(array $students): array;

    /**
     * @param Student[] $students
     * @return User[]
     */
    public function findAllStudentsByStudents(array $students): array;

    /**
     * @param Teacher[] $teachers
     * @return User[]
     */
    public function findAllTeachers(array $teachers): array;


    /**
     * @return User[]
     */
    public function findAllByNotifyExams(): array;

    /**
     * @return User[]
     */
    public function findAllByNotifySubstitutions(): array;

    /**
     * @return User[]
     */
    public function findAllByNotifyMessages(): array;

    /**
     * @param UserType[] $types
     * @return User[]
     */
    public function findAllByUserTypes(array $types): array;

    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param UserType[]|null $userTypes
     * @return PaginatedResult<User>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery, string $query, array|null $userTypes = null): PaginatedResult;

    /**
     * @param User $user
     */
    public function persist(User $user): void;

    /**
     * @param User $user
     */
    public function remove(User $user): void;

    /**
     * Removes orphaned users.
     *
     * @return int Number of users removed from the system.
     */
    public function removeOrphaned(): int;
}
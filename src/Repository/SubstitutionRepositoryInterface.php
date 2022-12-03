<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Substitution;
use App\Entity\Teacher;
use DateTime;

interface SubstitutionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Substitution|null
     */
    public function findOneById(int $id): ?Substitution;

    /**
     * @param string $externalId
     * @return Substitution|null
     */
    public function findOneByExternalId(string $externalId): ?Substitution;

    /**
     * @return Substitution[]
     */
    public function findAll();

    /**
     * @param bool $excludeNonStudentSubstitutions Whether or not to exclude all substitution without study groups assigned
     * @return Substitution[]
     */
    public function findAllByDate(DateTime $date, bool $excludeNonStudentSubstitutions = false);

    public function countAllByDate(DateTime $date): int;

    /**
     * @return Substitution[]
     */
    public function findAllForStudyGroups(array $studyGroups, ?DateTime $date = null);

    public function countAllForStudyGroups(array $studyGroups, ?DateTime $date = null): int;

    /**
     * @return Substitution[]
     */
    public function findAllForTeacher(Teacher $teacher, ?DateTime $date = null);

    public function countAllForTeacher(Teacher $teacher, ?DateTime $date = null): int;

    /**
     * @return Substitution[]
     */
    public function findAllForGrade(Grade $grade, ?DateTime $date = null);

    public function countAllForGrade(Grade $grade, ?DateTime $date = null): int;

    /**
     * @param Room[] $rooms
     * @param DateTime|null $date
     * @return Substitution[]
     */
    public function findAllForRooms(array $rooms, ?DateTime $date): array;

    /**
     * @param Substitution $substitution
     */
    public function persist(Substitution $substitution): void;

    /**
     * @param Substitution $substitution
     */
    public function remove(Substitution $substitution): void;
}
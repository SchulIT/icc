<?php

namespace App\Common\Repository;

use App\Common\Entity\Section;
use App\Common\Entity\Subject;
use App\Common\Entity\Teacher;
use App\Common\Entity\TeacherTag;
use App\Framework\Repository\TransactionalRepositoryInterface;
use DateTime;

interface TeacherRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Teacher|null
     */
    public function findOneById(int $id): ?Teacher;

    /**
     * @param string $uuid
     * @return Teacher|null
     */
    public function findOneByUuid(string $uuid): ?Teacher;

    /**
     * @param string $acronym
     * @return Teacher|null
     */
    public function findOneByAcronym(string $acronym): ?Teacher;

    /**
     * @param string $externalId
     * @return Teacher|null
     */
    public function findOneByExternalId(string $externalId): ?Teacher;

    /**
     * @param string $email
     * @return Teacher|null
     */
    public function findOneByEmailAddress(string $email): ?Teacher;

    /**
     * @param string[] $acronyms
     * @return Teacher[]
     */
    public function findAllByAcronym(array $acronyms): array;

    /**
     * Finds teachers with the given birthday (year is ignored) AND with the flag "showBirthday" set to true.
     *
     * @param DateTime $date
     * @return Teacher[]
     */
    public function findAllByBirthday(DateTime $date): array;

    /**
     * @param Subject|null $subject
     * @param TeacherTag|null $tag
     * @return Teacher[]
     */
    public function findAllBySubjectAndTag(?Subject $subject, ?TeacherTag $tag): array;

    /**
     * @return Teacher[]
     */
    public function findAll();

    /**
     * @param Section $section
     * @return Teacher[]
     */
    public function findAllBySection(Section $section): array;

    /**
     * @param Teacher $teacher
     */
    public function persist(Teacher $teacher): void;

    /**
     * @param Teacher $teacher
     */
    public function remove(Teacher $teacher): void;
}
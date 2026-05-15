<?php

namespace App\Common\Repository;

use App\Common\Entity\StudentInformation;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudentInformationType;
use DateTime;

interface StudentInformationRepositoryInterface {

    /**
     * @param Student[] $students
     * @param StudentInformationType|null $type
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return int
     */
    public function countByStudents(array $students, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): int;

    /**
     * @param Student[] $students
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return StudentInformation[]
     */
    public function findByStudents(array $students, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return StudentInformation[]
     */
    public function findByGrade(Grade $grade, Section $section, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array;

    /**
     * @param StudyGroup $studyGroup
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return StudentInformation[]
     */
    public function findByStudyGroup(StudyGroup $studyGroup, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array;

    public function removeExpired(DateTime $dateTime): int;

    public function persist(StudentInformation $information): void;

    public function remove(StudentInformation $information): void;
}
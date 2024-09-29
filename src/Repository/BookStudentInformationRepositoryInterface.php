<?php

namespace App\Repository;

use App\Entity\BookStudentInformation;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use DateTime;

interface BookStudentInformationRepositoryInterface {

    /**
     * @param Student[] $students
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return BookStudentInformation[]
     */
    public function findByStudents(array $students, DateTime|null $from = null, DateTime|null $until = null): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return BookStudentInformation[]
     */
    public function findByGrade(Grade $grade, Section $section, DateTime|null $from = null, DateTime|null $until = null): array;

    /**
     * @param StudyGroup $studyGroup
     * @param DateTime|null $from
     * @param DateTime|null $until
     * @return BookStudentInformation[]
     */
    public function findByStudyGroup(StudyGroup $studyGroup, DateTime|null $from = null, DateTime|null $until = null): array;

    public function removeExpired(DateTime $dateTime): int;

    public function persist(BookStudentInformation $information): void;

    public function remove(BookStudentInformation $information): void;
}
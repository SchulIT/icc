<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;

interface ExamRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Exam|null
     */
    public function findOneById(int $id): ?Exam;

    /**
     * @param string $externalId
     * @return Exam|null
     */
    public function findOneByExternalId(string $externalId): ?Exam;

    /**
     * @param Tuition[] $tuitions
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllByTuitions(array $tuitions, ?\DateTime $today = null);

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null);

    /**
     * @param Student[] $students
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllByStudents(array $students, ?\DateTime $today = null);

    /**
     * @param Grade $grade
     * @param \DateTime|null $today
     * @return Exam[]
     */
    public function findAllByGrade(Grade $grade, ?\DateTime $today = null);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAll(?\DateTime $today = null);

    /**
     * @param Exam $exam
     */
    public function persist(Exam $exam): void;

    /**
     * @param Exam $exam
     */
    public function remove(Exam $exam): void;
}
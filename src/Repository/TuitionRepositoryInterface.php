<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;

interface TuitionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Tuition|null
     */
    public function findOneById(int $id): ?Tuition;

    /**
     * @param string $externalId
     * @return Tuition|null
     */
    public function findOneByExternalId(string $externalId): ?Tuition;

    /**
     * @param string[] $externalIds
     * @return Tuition[]
     */
    public function findAllByExternalId(array $externalIds): array;

    /**
     * @param Teacher $teacher
     * @return Tuition[]
     */
    public function findAllByTeacher(Teacher $teacher);

    /**
     * @param Student[] $students
     * @return Tuition[]
     */
    public function findAllByStudents(array $students);

    /**
     * @param Grade[] $grades
     * @return Tuition[]
     */
    public function findAllByGrades(array $grades);

    /**
     * @param Subject[] $subjects
     * @return Tuition[]
     */
    public function findAllBySubjects(array $subjects);

    /**
     * @param string[] $grades
     * @param string $subjectOrCourse
     * @return Tuition[]
     */
    public function findAllByGradeAndSubjectOrCourseWithoutTeacher(array $grades, string $subjectOrCourse): array;

    /**
     * @param string[] $grades
     * @param string[] $teachers
     * @param string $subjectOrCourse
     * @return Tuition[]
     */
    public function findAllByGradeTeacherAndSubjectOrCourse(array $grades, array $teachers, string $subjectOrCourse): array;

    /**
     * @return Tuition[]
     */
    public function findAll();

    /**
     * @param Tuition $tuition
     */
    public function persist(Tuition $tuition): void;

    /**
     * @param Tuition $tuition
     */
    public function remove(Tuition $tuition): void;
}
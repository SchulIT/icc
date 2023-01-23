<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\Tuition;

interface TuitionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Tuition|null
     */
    public function findOneById(int $id): ?Tuition;

    /**
     * @param string $uuid
     * @return Tuition|null
     */
    public function findOneByUuid(string $uuid): ?Tuition;

    /**
     * @param string $externalId
     * @param Section $section
     * @return Tuition|null
     */
    public function findOneByExternalId(string $externalId, Section $section): ?Tuition;

    /**
     * @param string[] $externalIds
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllByExternalId(array $externalIds, Section $section): array;

    /**
     * @param Teacher $teacher
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllByTeacher(Teacher $teacher, Section $section): array;

    /**
     * @param Student[] $students
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllByStudents(array $students, Section $section): array;

    /**
     * @param Grade[] $grades
     * @param Section $section
     * @param bool $lazy
     * @return Tuition[]
     */
    public function findAllByGrades(array $grades, Section $section, bool $lazy = false): array;

    /**
     * @param Subject[] $subjects
     * @return Tuition[]
     */
    public function findAllBySubjects(array $subjects): array;

    /**
     * @param string[] $grades
     * @param string $subjectOrCourse
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllByGradeAndSubjectOrCourseWithoutTeacher(array $grades, string $subjectOrCourse, Section $section): array;

    /**
     * @param string[] $grades
     * @param string[] $teachers
     * @param string $subjectOrCourse
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllByGradeTeacherAndSubjectOrCourse(array $grades, array $teachers, string $subjectOrCourse, Section $section): array;

    /**
     * @param Substitution $substitution
     * @param Section $section
     * @return Tuition|null
     */
    public function findOneBySubstitution(Substitution $substitution, Section $section): ?Tuition;

    /**
     * @param Section $section
     * @return Tuition[]
     */
    public function findAllBySection(Section $section): array;

    /**
     * @param Tuition $tuition
     */
    public function persist(Tuition $tuition): void;

    /**
     * @param Tuition $tuition
     */
    public function remove(Tuition $tuition): void;
}
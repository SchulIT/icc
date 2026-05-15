<?php

namespace App\Student;

use App\Common\Entity\Grade;
use App\Common\Entity\GradeTeacher;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Repository\UserRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use DateTime;

readonly class RelatedUsersResolver {
    public function __construct(private UserRepositoryInterface $userRepository,
                                private SectionResolverInterface $sectionResolver) {

    }

    /**
     * @param Student|null $student
     * @param DateTime $date
     * @return User[]
     */
    public function resolveGradeTeachers(Student|null $student, DateTime $date): array {
        if($student === null) {
            return [ ];
        }

        $teachers = [ ];
        $section = $this->sectionResolver->getSectionForDate($date);
        /** @var Grade|null $grade */
        $grade = $student->getGrade($section);
        if($grade !== null && $section !== null) {
            /** @var GradeTeacher $teacher */
            foreach ($grade->getTeachers() as $teacher) {
                if($teacher->getSection()->getId() === $section->getId()) {
                    $teachers[] = $teacher->getTeacher();
                }
            }
        }

        return $this->userRepository->findAllTeachers($teachers);
    }

    public function resolveParents(Student|null $student): array {
        if($student === null) {
            return [ ];
        }

        return $this->userRepository->findAllParentsByStudents([$student]);
    }

    public function resolveStudents(Student|null $student): array {
        if($student === null) {
            return [ ];
        }

        return $this->userRepository->findAllStudentsByStudents([$student]);
    }

    public function resolveFullAgedStudents(Student|null $student, DateTime $referenceDate): array {
        if($student === null) {
            return [ ];
        }

        if($student->isFullAged($referenceDate) !== true) {
            return [ ];
        }

        return $this->userRepository->findAllStudentsByStudents([$student]);
    }
}
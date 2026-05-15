<?php

namespace App\StudentAbsence;

use App\Common\Entity\Grade;
use App\Common\Entity\GradeTeacher;
use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\Subject;
use App\Common\Entity\User;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Repository\UserRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Student\RelatedUsersResolver;
use App\Framework\Utils\ArrayUtils;
use SchulIT\CommonBundle\Helper\DateHelper;

readonly class InvolvedUsersResolver {

    public function __construct(private SectionResolverInterface $sectionResolver,
                                private UserRepositoryInterface $userRepository,
                                private TuitionRepositoryInterface $tuitionRepository,
                                private RelatedUsersResolver $relatedUsersResolver,
                                private DateHelper $dateHelper) {

    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveUsers(StudentAbsence $absence): array {
        return array_values(
            ArrayUtils::createArrayWithKeys(
                array_merge(
                    [$absence->getCreatedBy()],
                    $this->relatedUsersResolver->resolveGradeTeachers($absence->getStudent(), $absence->getFrom()->getDate()),
                    $this->relatedUsersResolver->resolveParents($absence->getStudent()),
                    $this->relatedUsersResolver->resolveFullAgedStudents($absence->getStudent(), $this->dateHelper->getToday()),
                    $this->resolveSubjectTeachersIfNecessary($absence)
                ),
                fn(User $user) => $user->getId()
            )
        );
    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveSubjectTeachersIfNecessary(StudentAbsence $absence): array {
        if($absence->getType()->getSubjects()->count() === 0 || $absence->getType()->isNotifySubjectTeacher() === false) {
            return [ ];
        }

        $subjectIds = $absence->getType()->getSubjects()->map(fn(Subject $subject) => $subject->getId())->toArray();

        $users = [ ];
        $tuitions = $this->tuitionRepository->findAllByStudents([$absence->getStudent()], $this->sectionResolver->getSectionForDate($absence->getFrom()->getDate()));

        foreach($tuitions as $tuition) {
            if($tuition->getSubject() === null || !in_array($tuition->getSubject()->getId(), $subjectIds)) {
                continue;
            }

            $users = array_merge($users, $this->userRepository->findAllTeachers($tuition->getTeachers()->toArray()));
        }

        return ArrayUtils::unique($users);
    }
}
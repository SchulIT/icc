<?php

namespace App\Security\Voter;

use App\Entity\BookIntegrityCheckViolation;
use App\Entity\GradeTeacher;
use App\Entity\User;
use App\Section\SectionResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookIntegrityCheckViolationVoter extends Voter {

    public const Suppress = 'suppress';

    public function __construct(private readonly SectionResolverInterface $sectionResolver) { }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::Suppress && $subject instanceof BookIntegrityCheckViolation;
    }

    /**
     * @param string $attribute
     * @param BookIntegrityCheckViolation $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User || $user->isTeacher() === false) {
            return false;
        }

        $section = $this->sectionResolver->getSectionForDate($subject->getDate());

        if($section === null) {
            return false;
        }

        $grade = $subject->getStudent()->getGrade($section);

        $gradeTeachers = $grade->getTeachers()
            ->filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection()->getId() === $section->getId())
            ->map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getTeacher());

        foreach($gradeTeachers as $teacher) {
            if($user->getTeacher()->getId() === $teacher->getId()) {
                return true;
            }
        }

        return false;
    }
}
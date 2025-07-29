<?php

namespace App\Security\Voter;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Exception\UnexpectedTypeException;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BirthdayVoter extends Voter {

    public const ShowBirthday = 'show-birthday';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool {
        return $attribute === self::ShowBirthday;
    }

    /**
     * @throws UnexpectedTypeException
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        if($attribute !== self::ShowBirthday) {
            throw new LogicException('This code should not be executed.');
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($this->accessDecisionManager->decide($token, ['ROLE_SHOW_BIRTHDAY']) !== true) {
            return false;
        }

        if($subject instanceof Teacher && ($subject->getBirthday() === null || $subject->isShowBirthday() !== true)) {
            return false;
        }

        if($subject instanceof Student && $user->isStudentOrParent()) {
            return $user->getStudents()->contains($subject);
        }

        return true;
    }
}
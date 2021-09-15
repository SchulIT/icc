<?php

namespace App\Security\Voter;

use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserType;
use App\Exception\UnexpectedTypeException;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BirthdayVoter extends Voter {

    const ShowBirthday = 'show-birthday';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject) {
        return $attribute === static::ShowBirthday;
    }

    /**
     * @param string $attribute
     * @param Student|null $subject
     * @param TokenInterface $token
     * @return bool|void
     * @throws UnexpectedTypeException
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        if($attribute !== static::ShowBirthday) {
            throw new LogicException('This code should not be executed.');
        }

        if($subject !== null && !$subject instanceof Student) {
            throw new UnexpectedTypeException($subject, Subject::class);
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($this->accessDecisionManager->decide($token, ['ROLE_SHOW_BIRTHDAY']) !== true) {
            return false;
        }

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ])) {
            if($subject === null) {
                return false;
            }

            return $user->getStudents()->contains($subject);
        }

        return true;
    }
}
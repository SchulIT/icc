<?php

namespace App\Security\Voter;

use App\Entity\StudentInformation;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentInformationVoter extends Voter {

    public const New = 'new-student-info';
    public const Edit = 'edit';
    public const Remove = 'remove';
    public const Show = 'show';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::New) {
            return true;
        }

        return in_array($attribute, [ self::Edit, self::Remove, self::Show])
            && $subject instanceof StudentInformation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::New:
                return $this->canAdd($token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);

            case self::Show:
                return $this->canShow($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canAdd(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canEdit(StudentInformation $information, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }
    
    private function canRemove(StudentInformation $information, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canShow(StudentInformation $information, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

}
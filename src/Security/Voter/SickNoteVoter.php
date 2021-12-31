<?php

namespace App\Security\Voter;

use App\Entity\SickNote;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SickNoteVoter extends Voter {

    public const New = 'new-sicknote';
    public const View = 'view';

    private DateHelper $dateHelper;
    private AccessDecisionManagerInterface $accessDicisionManager;

    public function __construct(DateHelper $dateHelper, AccessDecisionManagerInterface $accessDecisionManager) {
        $this->dateHelper = $dateHelper;
        $this->accessDicisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === static::New
            || ($attribute === static::View && $subject instanceof SickNote);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::View:
                return $this->canView($token, $subject);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token): bool {
        if($this->accessDicisionManager->decide($token, ['ROLE_SICK_NOTE_CREATOR']) === true || $this->accessDicisionManager->decide($token, ['ROLE_ADMIN']) ) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        if($isParent === true) {
            return true;
        }

        if($isStudent === false) {
            return false;
        }

        /** @var Student $student */
        foreach($user->getStudents() as $student) {
            if ($student->isFullAged($this->dateHelper->getToday()) === true) {
                return true;
            }
        }

        return false;
    }

    private function canView(TokenInterface $token, SickNote $sickNote): bool {
        if($this->accessDicisionManager->decide($token, ['ROLE_SICK_NOTE_VIEWER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        foreach($user->getStudents() as $student) {
            if($sickNote->getStudent() === $student) {
                return true;
            }
        }

        return false;
    }
}
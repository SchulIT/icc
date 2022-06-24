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
    public const CanViewAny = 'view-any-sicknote';

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
        return $attribute === self::New
            || $attribute === self::CanViewAny
            || ($attribute === self::View && $subject instanceof SickNote);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::View:
                return $this->canView($token, $subject);

            case self::CanViewAny:
                return $this->canViewAny($token);
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

    private function canViewAny(TokenInterface $token): bool {
        return $this->canCreate($token) || $this->accessDicisionManager->decide($token, ['ROLE_SICK_NOTE_VIEWER']) === true;
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
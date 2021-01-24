<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ListsVoter extends Voter {

    public const Teachers = 'teachers';
    public const Students = 'students';
    public const Tuitions = 'tuitions';
    public const StudyGroups = 'studygroups';
    public const Privacy = 'privacy';
    public const ExportTeachers = 'export-teachers';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Teachers,
            static::Students,
            static::Tuitions,
            static::StudyGroups,
            static::Privacy,
            static::ExportTeachers
        ];

        return in_array($attribute, $attributes)
            && $subject === null;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::Teachers:
                return true; // everyone can see teachers

            case static::Students:
            case static::StudyGroups:
            case static::Tuitions:
            case static::Privacy:
            case static::ExportTeachers:
                return $this->canViewLists($token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canViewLists(TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        return EnumArrayUtils::inArray($user->getUserType(), [
            UserType::Student(),
            UserType::Parent(),
            UserType::Intern()
        ]) !== true; // Everyone but students/parents/interns are allowed to view lists
    }
}
<?php

namespace App\Security\Voter;

use LogicException;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use phpDocumentor\Reflection\Utils;
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

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::Teachers,
            self::Students,
            self::Tuitions,
            self::StudyGroups,
            self::Privacy,
            self::ExportTeachers
        ];

        return in_array($attribute, $attributes)
            && $subject === null;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::Teachers => true,
            self::Students, self::StudyGroups, self::Tuitions, self::Privacy, self::ExportTeachers => $this->canViewLists($token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canViewLists(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return true;
        }

        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return EnumArrayUtils::inArray($user->getUserType(), [
            UserType::Student(),
            UserType::Parent(),
            UserType::Intern()
        ]) !== true; // Everyone but students/parents/interns are allowed to view lists
    }
}
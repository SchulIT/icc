<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserType;
use App\Utils\ArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ListsVoter extends Voter {

    public const Teachers = 'teachers';
    public const Students = 'students';
    public const Tuitions = 'tuitions';
    public const StudyGroups = 'studygroups';
    public const Privacy = 'privacy';

    public const LearningManagementSystems = 'lms';
    public const ExportLists = 'export-lists';

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
            self::LearningManagementSystems,
            self::ExportLists
        ];

        return in_array($attribute, $attributes)
            && $subject === null;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool
    {
        return match ($attribute) {
            self::Teachers => true,
            self::Students, self::StudyGroups, self::Tuitions, self::Privacy, self::LearningManagementSystems => $this->canViewLists($token),
            self::ExportLists => $this->canExport($token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    public function canExport(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_LISTS_EXPORTER']);
    }

    private function canViewLists(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_LISTS_VIEWER']);
    }
}
<?php

namespace App\Security\Voter;

use App\Entity\TimetablePeriod;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetablePeriodVoter extends Voter {

    const New = 'new-timetable-period';
    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Edit,
            static::Remove,
            static::View
        ];

        return $attribute === static::New
            || (in_array($attribute, $attributes) && $subject instanceof TimetablePeriod);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::View:
                return $this->canView($subject, $token);

            case static::New:
                return $this->canCreate($token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canView(TimetablePeriod $period, TokenInterface $token): bool {
        /** @var User $user */
        $user = $token->getUser();
        $userType = $user->getUserType();

        $allowedUserTypes = $period->getVisibilities()
            ->map(function(UserTypeEntity $visibility) {
                return $visibility->getUserType();
            })
            ->toArray();

        return EnumArrayUtils::inArray($userType, $allowedUserTypes);
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canEdit(TimetablePeriod $period, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canRemove(TimetablePeriod $period, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }
}
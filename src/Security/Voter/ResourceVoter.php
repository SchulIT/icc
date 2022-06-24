<?php

namespace App\Security\Voter;

use App\Entity\ResourceEntity;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter {

    public const New = 'new-room';
    public const View = 'view-rooms';
    public const Edit = 'edit';
    public const Remove = 'remove';

    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::Edit,
            self::Remove
        ];

        $staticAttributes = [
            self::New,
            self::View
        ];

        return in_array($attribute, $staticAttributes)
            || ($subject instanceof ResourceEntity && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canViewOverview($token);

            case self::New:
                return $this->canAdd($token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canAdd(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    public function canEdit(ResourceEntity $room, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    public function canRemove(ResourceEntity $room, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    private function canViewOverview(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        return EnumArrayUtils::inArray($user->getUserType(), [
                UserType::Student(),
                UserType::Parent()
            ]) !== true; // Everyone but students/parents are allowed to view lists
    }
}
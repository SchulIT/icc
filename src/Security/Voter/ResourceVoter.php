<?php

namespace App\Security\Voter;

use App\Entity\ResourceEntity;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\ArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter {

    public const New = 'new-room';
    public const View = 'view-rooms';
    public const Edit = 'edit';
    public const Remove = 'remove';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
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
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::View => $this->canViewOverview($token),
            self::New => $this->canAdd($token),
            self::Edit => $this->canEdit($subject, $token),
            self::Remove => $this->canRemove($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
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
        if($this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]) || $this->accessDecisionManager->decide($token, [ 'ROLE_RESOURCE_RESERVATION_VIEWER' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        return ArrayUtils::inArray($user->getUserType(), [
                UserType::Student,
                UserType::Parent
            ]) !== true; // Everyone but students/parents are allowed to view lists
    }
}
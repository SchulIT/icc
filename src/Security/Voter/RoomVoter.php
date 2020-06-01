<?php

namespace App\Security\Voter;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoomVoter extends Voter {

    public const New = 'new-room';
    public const View = 'view-rooms';
    public const Edit = 'edit';
    public const Remove = 'remove';

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
            static::Remove
        ];

        $staticAttributes = [
            static::New,
            static::View
        ];

        return in_array($attribute, $staticAttributes)
            || ($subject instanceof Room && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::View:
                return $this->canViewOverview($token);

            case static::New:
                return $this->canAdd($token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canAdd(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    public function canEdit(Room $room, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    public function canRemove(Room $room, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    private function canViewOverview(TokenInterface $token) {
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
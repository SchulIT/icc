<?php

namespace App\Security\Voter;

use App\Entity\RoomReservation;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoomReservationVoter extends Voter {

    public const New = 'new-reservation';
    public const View = 'view-reservations';
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
            || ($subject instanceof RoomReservation && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::View:
                return $this->canView($token);

            case static::New:
                return $this->canCreate($token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);

        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canView(TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]) === false;
    }

    private function canCreate(TokenInterface $token) {
        return $this->canView($token);
    }

    private function canEdit(RoomReservation $reservation, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->getTeacher() !== null && $reservation->getTeacher()->getId() === $user->getTeacher()) {
            return true;
        }

        return false;
    }

    private function canRemove(RoomReservation $reservation, TokenInterface $token) {
        return $this->canEdit($reservation, $token);
    }
}
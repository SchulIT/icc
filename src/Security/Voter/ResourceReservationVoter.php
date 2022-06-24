<?php

namespace App\Security\Voter;

use App\Entity\ResourceReservation;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceReservationVoter extends Voter {

    public const New = 'new-reservation';
    public const View = 'view-reservations';
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
            || ($subject instanceof ResourceReservation && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canView($token);

            case self::New:
                return $this->canCreate($token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);

        }

        throw new LogicException('This code should not be reached.');
    }

    private function canView(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]) === false;
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->canView($token);
    }

    private function canEdit(ResourceReservation $reservation, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->getTeacher() !== null && $reservation->getTeacher()->getId() === $user->getTeacher()->getId()) {
            return true;
        }

        return false;
    }

    private function canRemove(ResourceReservation $reservation, TokenInterface $token): bool {
        return $this->canEdit($reservation, $token);
    }
}
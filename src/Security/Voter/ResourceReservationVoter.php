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
            || ($subject instanceof ResourceReservation && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::View => $this->canView($token),
            self::New => $this->canCreate($token),
            self::Edit => $this->canEdit($subject, $token),
            self::Remove => $this->canRemove($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canView(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true || $this->accessDecisionManager->decide($token, ['ROLE_RESOURCE_RESERVATION_VIEWER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $user->isStudentOrParent() === false;
    }

    private function canCreate(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_RESOURCE_RESERVATION_CREATOR'])) {
            return true;
        }

        return $this->canView($token);
    }

    private function canEdit(ResourceReservation $reservation, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) === true || $this->accessDecisionManager->decide($token, ['ROLE_RESOURCE_RESERVATION_CREATOR']) === true) {
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
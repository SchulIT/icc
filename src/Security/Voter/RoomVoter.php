<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoomVoter extends Voter {

    public const View = 'view-rooms';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::View;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::View:
                return $this->canViewOverview($token);
        }

        throw new \LogicException('This code should not be reached.');
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
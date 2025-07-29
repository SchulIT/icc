<?php

namespace App\Security\Voter;

use App\Entity\ResourceType;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceTypeVoter extends Voter {

    public const New = 'new-resource-type';
    public const Edit = 'edit';
    public const Remove = 'remove';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::New
            || ($subject instanceof ResourceType && in_array($attribute, [ self::Edit, self::Remove ]));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool
    {
        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::Edit => $this->canEdit($subject, $token),
            self::Remove => $this->canRemove($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    private function canEdit(ResourceType $type, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_ADMIN' ]);
    }

    private function canRemove(ResourceType $type, TokenInterface $token): bool {
        return $this->canEdit($type, $token) && $type->getId() != 1 && $type->getResources()->count() === 0;
    }
}
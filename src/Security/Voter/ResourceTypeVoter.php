<?php

namespace App\Security\Voter;

use App\Entity\ResourceType;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceTypeVoter extends Voter {

    public const New = 'new-resource-type';
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
        return $attribute === static::New
            || ($subject instanceof ResourceType && in_array($attribute, [ static::Edit, static::Remove ]));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
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
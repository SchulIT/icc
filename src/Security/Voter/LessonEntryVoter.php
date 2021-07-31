<?php

namespace App\Security\Voter;

use App\Entity\LessonEntry;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LessonEntryVoter extends Voter {

    public const New = 'new-entry';
    public const Edit = 'edit';
    public const Remove = 'remove';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject) {
        $attributes = [
            static::Edit,
            static::Remove
        ];

        return $attribute === static::New
            || (in_array($attribute, $attributes) && $subject instanceof LessonEntry);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::Edit:
            case static::Remove:
                return $this->canEditOrRemove($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    public function canEditOrRemove(LessonEntry $lessonEntry, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }
}
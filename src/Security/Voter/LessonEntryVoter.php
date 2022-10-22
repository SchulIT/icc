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

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool {
        $attributes = [
            self::Edit,
            self::Remove
        ];

        return $attribute === self::New
            || (in_array($attribute, $attributes) && $subject instanceof LessonEntry);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::Edit, self::Remove => $this->canEditOrRemove($subject, $token),
            default => throw new LogicException('This code should not be reached.'),
        };
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    public function canEditOrRemove(LessonEntry $lessonEntry, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }
}
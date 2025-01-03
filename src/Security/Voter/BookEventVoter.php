<?php

namespace App\Security\Voter;

use App\Book\Export\Book;
use App\Entity\BookEvent;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookEventVoter extends Voter {

    public const New = 'new-bookevent';
    public const View = 'view';
    public const Edit = 'edit';
    public const Show = 'show';
    public const Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::New) {
            return true;
        }

        return in_array($attribute, [self::View, self::Edit, self::Show, self::Remove])
            && $subject instanceof BookEvent;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
            case self::Show:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canView(BookEvent $event, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canEdit(BookEvent $event, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_BOOK_ENTRY_CREATOR']);
    }

    private function canRemove(BookEvent $event, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User || !$user->isTeacher()) {
            return false;
        }

        return $this->canEdit($event, $token)
            && $event->getTeacher()?->getId() === $user->getTeacher()->getId();
    }
}
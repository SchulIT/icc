<?php

namespace App\Security\Voter;

use App\Entity\ChatMessage;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatMessageVoter extends Voter {
    public const View = 'view';
    public const Edit = 'edit';
    public const Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }


    protected function supports(string $attribute, mixed $subject): bool {
        return $subject instanceof ChatMessage && in_array($attribute, [self::View, self::Edit, self::Remove], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canView(ChatMessage $message, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ ChatVoter::View], $message->getChat());
    }

    private function canEdit(ChatMessage $message, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User || $message->getCreatedBy() === null) {
            return false;
        }

        return $user->getId() === $message->getCreatedBy()->getId();
    }

    private function canRemove(ChatMessage $message, TokenInterface $token): bool {
        return $this->canEdit($message, $token);
    }
}
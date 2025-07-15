<?php

namespace App\Security\Voter;

use App\Entity\ChatMessage;
use App\Entity\User;
use App\Settings\ChatSettings;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatMessageVoter extends Voter {
    public const string CanViewReadConfirmations = 'can-view-read-confirmations';
    public const string View = 'view';
    public const string Edit = 'edit';
    public const string Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager, private readonly ChatSettings $chatSettings) {

    }


    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::CanViewReadConfirmations) {
            return true;
        }

        return $subject instanceof ChatMessage && in_array($attribute, [self::View, self::Edit, self::Remove], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::CanViewReadConfirmations:
                return $this->canViewReadConfirmations($token);

            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canViewReadConfirmations(TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return in_array($user->getUserType(), $this->chatSettings->getUserTypesAllowedToSeeReadConfirmations(), true);
    }

    private function canView(ChatMessage $message, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ ChatVoter::View], $message->getChat());
    }

    private function canEdit(ChatMessage $message, TokenInterface $token): bool {
        if($message->getChat()->isArchived()) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User || $message->getCreatedBy() === null) {
            return false;
        }

        if(!in_array($user->getUserType(), $this->chatSettings->getUserTypesAllowedToEditOrRemoveMessages(), true)) {
            return false;
        }

        return $user->getId() === $message->getCreatedBy()->getId();
    }

    private function canRemove(ChatMessage $message, TokenInterface $token): bool {
        return $this->canEdit($message, $token);
    }
}
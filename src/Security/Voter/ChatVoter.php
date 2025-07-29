<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use App\Entity\User;
use App\Settings\ChatSettings;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatVoter extends Voter {

    public const string ChatEnabled = 'is-chat-enabled';
    public const string View = 'view';
    public const string Remove = 'remove';
    public const string Edit = 'edit';
    public const string Archive = 'archive';
    public const string Unarchive = 'unarchive';
    public const string Reply = 'reply';
    public const string Participants = 'participants';

    public function __construct(private readonly ChatSettings $chatSettings, private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::ChatEnabled) {
            return true;
        }

        return $subject instanceof Chat && in_array($attribute, [self::View, self::Remove, self::Edit, self::Archive, self::Unarchive, self::Reply, self::Participants], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        switch($attribute) {
            case self::ChatEnabled:
                return $this->isEnabled($token);

            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);

            case self::Archive:
            case self::Unarchive:
                return $this->canArchive($token);

            case self::Reply:
                return $this->canReply($subject, $token);

            case self::Participants:
                return $this->canAddOrRemoveParticipants($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function isEnabled(TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if (in_array($user->getUserType(), $this->chatSettings->getEnabledUserTypes(), true)) {
            return true;
        }

        return false;
    }

    private function canView(Chat $chat, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        foreach($chat->getParticipants() as $participant) {
            if($participant->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Chat $chat, TokenInterface $token): bool {
        if($chat->isArchived()) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [ 'ROLE_CHAT_MOD' ]);
    }

    private function canRemove(Chat $chat, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [ 'ROLE_CHAT_MOD' ]);
    }

    private function canArchive(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_CHAT_MOD' ]);
    }

    private function canReply(Chat $chat, TokenInterface $token): bool {
        if($this->canView($chat, $token) !== true) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($chat->isArchived()) {
            return false;
        }

        foreach($chat->getParticipants() as $participant) {
            if($participant->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    private function canAddOrRemoveParticipants(Chat $chat, TokenInterface $token): bool {
        return $this->canEdit($chat, $token);
    }
}
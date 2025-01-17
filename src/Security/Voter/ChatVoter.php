<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use App\Entity\User;
use App\Settings\ChatSettings;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatVoter extends Voter {

    public const ChatEnabled = 'is-chat-enabled';

    public const View = 'view';

    public const Remove = 'remove';

    public const Edit = 'edit';

    public function __construct(private readonly ChatSettings $chatSettings, private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::ChatEnabled) {
            return true;
        }

        return $subject instanceof Chat && in_array($attribute, [self::View, self::Remove, self::Edit], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::ChatEnabled:
                return $this->isEnabled($token);

            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($token);

            case self::Remove:
                return $this->canRemove($token);
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

    private function canEdit(TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [ 'ROLE_CHAT_MOD' ]);
    }

    private function canRemove(TokenInterface $token): bool {
        return $this->canEdit($token);
    }
}
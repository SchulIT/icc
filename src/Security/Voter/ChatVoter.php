<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use App\Settings\ChatSettings;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatVoter extends Voter {

    public const ChatEnabled = 'is-chat-enabled';

    public const View = 'view';

    public const Remove = 'remove';

    public function __construct(private readonly ChatSettings $chatSettings) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::ChatEnabled) {
            return true;
        }

        return $subject instanceof Chat && in_array($attribute, [self::View, self::Remove], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::ChatEnabled:
                return $this->isEnabled($token);

            case self::View:
                return $this->canView($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
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

    private function canView(Chat|ChatMessage $chatOrMessage, TokenInterface $token): bool {
        if($chatOrMessage instanceof ChatMessage) {
            return $this->canView($chatOrMessage->getChat(), $token);
        }

        /** @var Chat $chatOrMessage */

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        foreach($chatOrMessage->getParticipants() as $participant) {
            if($participant->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
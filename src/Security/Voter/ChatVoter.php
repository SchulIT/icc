<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\ChatMessageAttachment;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatVoter extends Voter {

    public const View = 'view';

    public const Download = 'download';

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::View && ($subject instanceof Chat || $subject instanceof ChatMessage)) {
            return true;
        } else if($attribute === self::Download && $subject instanceof ChatMessageAttachment) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canView($subject, $token);

            case self::Download:
                return $this->canDownload($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
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

    private function canDownload(ChatMessageAttachment $attachment, TokenInterface $token): bool {
        return $this->canView($attachment->getMessage(), $token);
    }
}
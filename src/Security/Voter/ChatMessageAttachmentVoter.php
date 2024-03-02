<?php

namespace App\Security\Voter;

use App\Entity\ChatMessageAttachment;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatMessageAttachmentVoter extends Voter {

    public const Download = 'download';

    public const Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        return in_array($attribute, [self::Download, self::Remove]) && $subject instanceof ChatMessageAttachment;
    }

    /**
     * @param string $attribute
     * @param ChatMessageAttachment $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::Download:
                return $this->canDownload($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canDownload(ChatMessageAttachment $attachment, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ChatVoter::View], $attachment->getMessage()->getChat());
    }

    private function canRemove(ChatMessageAttachment $attachment, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $attachment->getMessage()->getCreatedBy()?->getId() === $user->getId();
    }
}
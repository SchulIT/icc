<?php

namespace App\Security\Voter;

use App\Entity\TimetableLessonAdditionalInformation;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetableLessonAdditionalInformationVoter extends Voter {

    public const New = 'new-timetablelesson-additionalinformation';

    public const Edit = 'edit';

    public const Remove = 'remove';

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::New) {
            return true;
        }

        return $subject instanceof TimetableLessonAdditionalInformation
            && in_array($attribute, [self::Edit, self::Remove]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        switch($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::Edit:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canCreate(TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $user->isTeacher();
    }

    private function canEdit(TimetableLessonAdditionalInformation $additionalInformation, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User || $user->getTeacher() === null) {
            return false;
        }

        return $additionalInformation->getAuthor()->getId() === $user->getTeacher()->getId();
    }

    private function canRemove(TimetableLessonAdditionalInformation $additionalInformation, TokenInterface $token): bool {
        return $this->canEdit($additionalInformation, $token);
    }
}
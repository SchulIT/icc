<?php

namespace App\Security\Voter;

use App\Entity\TeacherTag;
use App\Entity\User;
use App\Entity\UserTypeEntity;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeacherTagVoter extends Voter {

    public const View = 'view';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $subject instanceof TeacherTag && $attribute === self::View;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        if ($attribute == self::View) {
            return $this->canView($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    public function canView(TeacherTag $tag, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return EnumArrayUtils::inArray($user->getUserType(), $tag->getVisibilities()->map(fn(UserTypeEntity $entity) => $entity->getUserType()));
    }
}
<?php

namespace App\Security\Voter;

use App\Entity\ExcuseNote;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExcuseNoteVoter extends Voter {
    public const New = 'new-excuse';
    public const Edit = 'edit';
    public const Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) { }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::New
            || (in_array($attribute, [ self::Edit, self::Remove ]) && $subject instanceof ExcuseNote);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        switch($attribute) {
            case self::New:
            case self::Edit:
            case self::Remove:
                return $this->accessDecisionManager->decide($token, [ 'ROLE_BOOK_VIEWER']) && $user->getTeacher() !== null;
        }

        throw new LogicException('This code should not be reached.');
    }
}
<?php

namespace App\Security\Voter;

use App\Entity\ExcuseNote;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExcuseNoteVoter extends Voter {
    public const New = 'new-excuse';
    public const Edit = 'edit';
    public const Remove = 'remove';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager, private readonly FeatureManager $featureManager) { }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::New
            || (in_array($attribute, [ self::Edit, self::Remove ]) && $subject instanceof ExcuseNote);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        if($this->featureManager->isFeatureEnabled(Feature::Book) !== true) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        switch($attribute) {
            case self::New:
            case self::Edit:
            case self::Remove:
                return $this->accessDecisionManager->decide($token, [ 'ROLE_BOOK_ENTRY_CREATOR']) && $user->getTeacher() !== null;
        }

        throw new LogicException('This code should not be reached.');
    }
}
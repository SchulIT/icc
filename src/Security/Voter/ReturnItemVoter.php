<?php

namespace App\Security\Voter;

use App\Entity\ReturnItem;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReturnItemVoter extends Voter {

    public const string New = 'new-return-item';
    public const string Edit = 'edit';
    public const string Remove = 'remove';
    public const string Show = 'show';

    public const string Return = 'return';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager, private readonly FeatureManager $featureManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::New ||
            ($subject instanceof ReturnItem && in_array($attribute, [self::Edit, self::Remove, self::Show, self::Return]));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        if($this->featureManager->isFeatureEnabled(Feature::ReturnItem) !== true) {
            return false;
        }

        switch($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::Edit:
            case self::Remove:
                return $this->canEditOrRemove($subject, $token);

            case self::Show:
                return $this->canView($subject, $token);

            case self::Return:
                return $this->canReturn($subject, $token);

            default:
                throw new LogicException('This code should not be executed.!');
        }
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_RETURN_ITEM_CREATOR']);
    }

    private function canEditOrRemove(ReturnItem $returnItem, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($returnItem->getCreatedBy()?->getId() === $user->getId()) {
            // only creator can remove return item
            return true;
        }

        return false;
    }

    private function canReturn(ReturnItem $returnItem, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_RETURN_ITEM_CREATOR'])
            && $this->canView($returnItem, $token);
    }

    private function canView(ReturnItem $returnItem, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_RETURN_ITEM_CREATOR'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        foreach($user->getStudents() as $student) {
            if($student->getId() === $returnItem->getStudent()->getId()) {
                return true;
            }
        }

        return false;
    }
}
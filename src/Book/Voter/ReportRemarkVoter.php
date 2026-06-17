<?php

namespace App\Book\Voter;

use App\Book\Entity\ReportRemark;
use App\Common\Entity\User;
use LogicException;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReportRemarkVoter extends Voter {

    public const NEW = "new-report-remark";
    public const EDIT = 'edit';
    public const REMOVE = 'remove';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {

    }

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::NEW ||
            ($subject instanceof ReportRemark && in_array($attribute, [self::EDIT, self::REMOVE]));
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::NEW:
                return $this->canCreate($token);

            case self::EDIT:
            case self::REMOVE:
                return $this->canEdit($subject, $token);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canCreate(TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $user->isTeacher();
    }

    private function canEdit(ReportRemark $remark, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $remark->getCreatedBy()?->getId() === $user->getId();
    }
}

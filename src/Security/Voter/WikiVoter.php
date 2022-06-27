<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserTypeEntity;
use App\Entity\WikiArticle;
use App\Utils\EnumArrayUtils;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiVoter extends Voter {

    public const View = 'view';
    public const New = 'new-wiki-article';
    public const Edit = 'edit';
    public const Remove = 'remove';

    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::View,
            self::Edit,
            self::Remove
        ];

        return $attribute === self::New ||
              (in_array($attribute, $attributes) && $subject instanceof WikiArticle);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($token);

            case self::Remove:
                return $this->canRemove($token);

            case self::New:
                return $this->canCreate($token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canView(WikiArticle $article, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN'])) {
            // Admins can view all documents
            return true;
        }

        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        $currentArticle = $article;
        do {
            $visibilities = $currentArticle->getVisibilities()
                ->map(function(UserTypeEntity $visibility) {
                    return $visibility->getUserType();
                })
                ->toArray();

            if(count($visibilities) > 0 && EnumArrayUtils::inArray($user->getUserType(), $visibilities) !== true) {
                return false;
            }

            $currentArticle = $currentArticle->getParent();
        } while($currentArticle !== null);

        return true;
    }

    private function canEdit(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canRemove(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }
}
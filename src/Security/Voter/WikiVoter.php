<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserTypeEntity;
use App\Entity\WikiArticle;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiVoter extends Voter {

    public const View = 'view';
    public const New = 'new-wiki-article';
    public const Edit = 'edit';
    public const Remove = 'remove';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::View,
            static::Edit,
            static::Remove
        ];

        return $attribute === static::New ||
              (in_array($attribute, $attributes) && $subject instanceof WikiArticle);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::View:
                return $this->canView($subject, $token);

            case static::Edit:
                return $this->canEdit($token);

            case static::Remove:
                return $this->canRemove($token);

            case static::New:
                return $this->canCreate($token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canView(WikiArticle $article, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN'])) {
            // Admins can view all documents
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

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

    private function canEdit(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canRemove(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canCreate(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }
}
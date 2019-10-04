<?php

namespace App\Security\Voter;

use App\Entity\WikiArticle;
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
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);

            case static::New:
                return $this->canCreate($token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canView(WikiArticle $article, TokenInterface $token) {
        // TODO
        return true;
    }

    private function canEdit(WikiArticle $article, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canRemove(WikiArticle $article, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }

    private function canCreate(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_WIKI_ADMIN']);
    }
}
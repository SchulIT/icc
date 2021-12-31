<?php

namespace App\Twig;

use App\Entity\User;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVariable {
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        return $user;
    }

    public function getFirstname(): string {
        return $this->getUser()->getFirstname();
    }

    public function getLastname(): string {
        return $this->getUser()->getLastname();
    }

    public function getServices() {
        $token = $this->tokenStorage->getToken();

        if($token instanceof SamlSpToken) {
            return $token->getAttribute('services');
        }

        return [ ];
    }
}
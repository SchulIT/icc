<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityUser implements UserInterface {

    private $username;
    private $roles;

    public function __construct(User $user) {
        $this->username = $user->getUsername();
        $this->roles = $user->getRoles();
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }
}
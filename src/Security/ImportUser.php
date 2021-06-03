<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ImportUser implements UserInterface {

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return ['ROLE_IMPORT'];
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
        return 'import-user';
    }

    public function getUserIdentifier(): string {
        return $this->getUsername();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }
}
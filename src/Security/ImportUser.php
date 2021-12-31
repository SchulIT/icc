<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ImportUser implements UserInterface {

    /**
     * @inheritDoc
     */
    public function getRoles(): array {
        return ['ROLE_IMPORT'];
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string {
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
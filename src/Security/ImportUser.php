<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ImportUser implements UserInterface {

    public function getRoles(): array {
        return ['ROLE_IMPORT'];
    }

    public function getPassword(): ?string {
        return '';
    }

    public function getSalt(): ?string {
        return null;
    }

    public function getUsername(): string {
        return 'import-user';
    }

    public function getUserIdentifier(): string {
        return $this->getUsername();
    }

    public function eraseCredentials(): void { }
}
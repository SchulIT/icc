<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Security;

class CurrentUserResolver {
    private $security;
    private $userRepository;

    private $username = null;
    private $user = null;

    public function __construct(Security $security, UserRepositoryInterface $userRepository) {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function hasUser(): bool {
        return $this->security->getUser() instanceof SecurityUser;
    }

    public function getUser(): ?User {
        if($this->security->getUser()->getUsername() !== $this->username || $this->user === null) {
            $securityUser = $this->security->getUser();

            $this->user = $this->userRepository->findOneByUsername($securityUser->getUsername());
            $this->username = $securityUser->getUsername();
        }

        return $this->user;
    }
}
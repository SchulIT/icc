<?php

namespace App\Security;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SecurityUserProvider implements UserProviderInterface {

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username) {
        $user = $this->userRepository->findOneByUsername($username);

        if($user === null) {
            throw new UsernameNotFoundException(sprintf('No such user "%s"', $username));
        }

        return new SecurityUser($user);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class) {
        return $class === SecurityUser::class;
    }
}
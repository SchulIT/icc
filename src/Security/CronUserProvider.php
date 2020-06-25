<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CronUserProvider implements UserProviderInterface {

    private $username;
    private $password;

    public function __construct(string $username, string $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function loadUserByUsername($username) {
        if($username !== $this->username) {
            throw new UsernameNotFoundException();
        }

        return new User($this->username, $this->password, [ 'ROLE_CRON' ]);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($this->username);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class) {
        return $class === User::class;
    }
}
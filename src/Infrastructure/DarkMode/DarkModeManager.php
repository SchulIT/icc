<?php

namespace App\Infrastructure\DarkMode;

use App\Common\Entity\User;
use App\Common\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DarkModeManager implements DarkModeManagerInterface {

    private const string Key = 'settings.dark_mode.enabled';

    public function __construct(private TokenStorageInterface $tokenStorage, private UserRepositoryInterface $userRepository)
    {
    }

    private function getUser(): ?User {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    private function setDarkMode(bool $isDarkModeEnabled): void {
        $user = $this->getUser();

        if ($user !== null) {
            $user->setData(self::Key, $isDarkModeEnabled);
            $this->userRepository->persist($user);
        }
    }

    public function enableDarkMode(): void {
        $this->setDarkMode(true);
    }

    public function disableDarkMode(): void {
        $this->setDarkMode(false);
    }

    public function isDarkModeEnabled(): bool {
        $user = $this->getUser();

        if ($user !== null) {
            return $user->getData(self::Key, false) === true;
        }

        return false;
    }
}
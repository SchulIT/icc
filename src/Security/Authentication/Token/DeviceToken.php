<?php

namespace App\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use App\Entity\IcsAccessToken as DeviceTokenEntity;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceToken extends AbstractToken {

    private string $token;
    private ?DeviceTokenEntity $device;

    public function __construct(string $token, DeviceTokenEntity $device = null, array $roles = []) {
        parent::__construct($roles);

        $this->token = $token;
        $this->device = $device;
    }

    public function getDevice(): ?DeviceTokenEntity {
        return $this->device;
    }

    public function getUser(): ?UserInterface {
        return $this->device;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials() {
        return $this->token;
    }

    public function getToken() {
        return $this->token;
    }
}
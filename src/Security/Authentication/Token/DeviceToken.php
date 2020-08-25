<?php

namespace App\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use App\Entity\IcsAccessToken as DeviceTokenEntity;

class DeviceToken extends AbstractToken {

    private $token;
    private $device;

    public function __construct(string $token, DeviceTokenEntity $device = null, array $roles = []) {
        parent::__construct($roles);

        $this->token = $token;
        $this->device = $device;

        $this->setAuthenticated(count($roles) > 0);
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
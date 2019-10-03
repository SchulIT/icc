<?php

namespace App\Security\Authentication\Provider;

use App\Security\Authentication\Token\DeviceToken;
use App\Security\Devices\DeviceManager;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DeviceTokenProvider implements AuthenticationProviderInterface {

    private $deviceManager;

    public function __construct(DeviceManager $deviceManager) {
        $this->deviceManager = $deviceManager;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(TokenInterface $token) {
        /** @var DeviceToken $deviceToken */
        $deviceToken = $token;
        $device = $this->deviceManager->getDeviceToken($deviceToken->getToken());

        if($device !== null) {
            $user = $device->getUser();
            $authenticatedToken = new DeviceToken($deviceToken->getToken(), $device, $device->getUser()->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('Device authentication failed due to incorrect credentials.');
    }

    /**
     * @inheritDoc
     */
    public function supports(TokenInterface $token) {
        return $token instanceof DeviceToken;
    }
}
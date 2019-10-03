<?php

namespace App\Security\Devices;

use App\Entity\DeviceToken;
use App\Utils\SecurityUtilsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DeviceManager {
    private $em;
    private $security;
    private $utils;

    public function __construct(EntityManagerInterface $manager, Security $security, SecurityUtilsInterface $securityUtils) {
        $this->em = $manager;
        $this->security = $security;
        $this->utils = $securityUtils;
    }

    public function getDeviceToken(string $token): ?DeviceToken {
        $token = $this->em->getRepository(DeviceToken::class)
            ->findOneBy([
                'token' => $token
            ]);

        return $token;
    }

    public function persistDeviceToken(DeviceToken $deviceToken): DeviceToken {
        $repository = $this->em->getRepository(DeviceToken::class);

        do {
            $deviceToken->setToken($this->utils->generateRandom(128));
        } while($repository->findOneBy(['token' => $deviceToken->getToken()]) !== null);

        $this->em->persist($deviceToken);
        $this->em->flush();

        return $deviceToken;
    }

    public function removeDeviceToken(DeviceToken $deviceToken) {
        // TODO
    }
}
<?php

namespace App\Security\IcsAccessToken;

use App\Entity\IcsAccessToken;
use App\Utils\SecurityUtilsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class IcsAccessTokenManager {
    private EntityManagerInterface $em;
    private SecurityUtilsInterface $utils;

    public function __construct(EntityManagerInterface $manager, SecurityUtilsInterface $securityUtils) {
        $this->em = $manager;
        $this->utils = $securityUtils;
    }

    public function getToken(string $token): ?IcsAccessToken {
        /** @var IcsAccessToken|null $token */
        $token = $this->em->getRepository(IcsAccessToken::class)
            ->findOneBy([
                'token' => $token
            ]);

        return $token;
    }

    public function persistToken(IcsAccessToken $deviceToken): IcsAccessToken {
        $repository = $this->em->getRepository(IcsAccessToken::class);

        do {
            $deviceToken->setToken($this->utils->generateRandom(128));
        } while($repository->findOneBy(['token' => $deviceToken->getToken()]) !== null);

        $this->em->persist($deviceToken);
        $this->em->flush();

        return $deviceToken;
    }
}
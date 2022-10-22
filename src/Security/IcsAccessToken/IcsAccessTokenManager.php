<?php

namespace App\Security\IcsAccessToken;

use App\Entity\IcsAccessToken;
use App\Utils\SecurityUtilsInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class IcsAccessTokenManager {
    public function __construct(private EntityManagerInterface $em, private SecurityUtilsInterface $utils)
    {
    }

    public function getToken(string $token): ?IcsAccessToken {
        /** @var IcsAccessToken|null $token */
        $token = $this->em->getRepository(IcsAccessToken::class)
            ->findOneBy([
                'token' => $token
            ]);

        return $token;
    }

    public function setLastActive(IcsAccessToken $accessToken): void {
        $accessToken->setLastActive(new DateTime());
        $this->em->persist($accessToken);
        $this->em->flush();
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
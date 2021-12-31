<?php

namespace App\Security\OAuth2;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\AccessToken;
use Trikoder\Bundle\OAuth2Bundle\Model\RefreshToken;

class AppManager {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @return AccessToken[]
     */
    public function getAccessTokens(User $user): array {
        $criteria = [
            'userIdentifier' => $user->getUsername(),
            'revoked' => false
        ];

        return $this->em->getRepository(AccessToken::class)
            ->findBy($criteria);
    }

    private function getRefreshToken(AccessToken $token): ?RefreshToken {
        return $this->em->getRepository(RefreshToken::class)
            ->findOneBy([
                'accessToken' => $token,
                'revoked' => false
            ]);
    }

    public function revokeAccessToken(AccessToken $token): void {
        $refreshToken = $this->getRefreshToken($token);

        if($refreshToken !== null) {
            $refreshToken->revoke();
        }
        $token->revoke();

        $this->em->persist($refreshToken);
        $this->em->persist($token);

        $this->em->flush();
    }
}
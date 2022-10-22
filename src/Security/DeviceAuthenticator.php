<?php

namespace App\Security;

use App\Security\IcsAccessToken\IcsAccessTokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class DeviceAuthenticator extends AbstractAuthenticator {

    public function __construct(private IcsAccessTokenManager $deviceManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool {
        return $request->attributes->has('token')
            && !empty($request->attributes->get('token'));
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport|SelfValidatingPassport {
        $token = $request->attributes->get('token');
        $icsToken = $this->deviceManager->getToken($token);

        if($icsToken === null) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        $this->deviceManager->setLastActive($icsToken);

        return new SelfValidatingPassport(new UserBadge($icsToken->getUser()->getUserIdentifier()));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new Response('', Response::HTTP_FORBIDDEN);
    }
}
<?php

namespace App\Security;

use App\Response\ErrorResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ImportUserAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface {

    public const HeaderKey = 'X-Token';

    public function __construct(private string $presharedKey, private SerializerInterface $serializer)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool {
        return $request->headers->has(self::HeaderKey);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null): Response {
        $json = $this->serializer->serialize(
            new ErrorResponse($authException != null ? $authException->getMessage() : 'Authentication required.')
            , 'json');

        return new Response($json, Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
        $json = $this->serializer->serialize(
            new ErrorResponse($exception->getMessage())
            , 'json');

        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport {
        $token = $request->headers->get(self::HeaderKey);

        if($token === null) {
            throw new CustomUserMessageAuthenticationException('No token provided.');
        }

        if($token !== $this->presharedKey) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        return new SelfValidatingPassport(new UserBadge('import'));
    }
}
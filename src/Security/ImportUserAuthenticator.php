<?php

namespace App\Security;

use App\Response\ErrorResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ImportUserAuthenticator extends AbstractGuardAuthenticator {

    public const HeaderKey = 'X-Token';

    private $presharedKey;
    private $serializer;

    public function __construct(string $presharedKey, SerializerInterface $serializer) {
        $this->presharedKey = $presharedKey;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        $json = $this->serializer->serialize(
            new ErrorResponse($authException != null ? $authException->getMessage() : 'Authentication required.')
            , 'json');

        return new Response($json, Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) {
        return $request->headers->has(static::HeaderKey);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) {
        return [
            'token' => $request->headers->get(self::HeaderKey)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        return new ImportUser();
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        return $credentials['token'] === $this->presharedKey;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $json = $this->serializer->serialize(
            new ErrorResponse($exception->getMessage())
            , 'json');

        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe() {
        return false;
    }
}
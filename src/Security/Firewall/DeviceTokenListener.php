<?php

namespace App\Security\Firewall;

use App\Security\Authentication\Token\DeviceToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DeviceTokenListener {

    private $tokenStorage;
    private $authenticationManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    public function __invoke(RequestEvent $event) {
        $request = $event->getRequest();
        $requestToken = $request->attributes->get('token');

        if(empty($requestToken)) {
            throw new AuthenticationException('You must provide an authentication token.');
        }

        $token = new DeviceToken($requestToken);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);

            return;
        } catch (AuthenticationException $e) {
            // TODO
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);

        $event->setResponse($response);
    }
}
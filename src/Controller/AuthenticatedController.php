<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticatedController extends AbstractController {
    #[Route('/authenticated', name: 'xhr_authenticated', methods: ['GET'])]
    public function testAuthentication(TokenStorageInterface $tokenStorage): JsonResponse {
        $result = [
            'authenticated' => $this->isAuthenticated($tokenStorage)
        ];

        return new JsonResponse($result);
    }

    private function isAuthenticated(TokenStorageInterface $tokenStorage): bool {
        $token = $tokenStorage->getToken();

        if(!$token instanceof TokenInterface) {
            return false;
        }

        if($token->getUser() === null) {
            return false;
        }

        return true;
    }
}
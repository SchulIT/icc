<?php

namespace App\ArgumentResolver;

use App\Entity\User;
use App\Security\CurrentUserResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserValueResolver implements ArgumentValueResolverInterface {

    private $currentUserResolver;

    public function __construct(CurrentUserResolver $currentUserResolver) {
        $this->currentUserResolver = $currentUserResolver;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument) {
        if($argument->getType() !== User::class) {
            return false;
        }

        return $this->currentUserResolver->hasUser();
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument) {
        yield $this->currentUserResolver->getUser();
    }
}
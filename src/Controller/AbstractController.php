<?php

namespace App\Controller;

use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends SymfonyAbstractController {

    public function __construct(private RefererHelper $redirectHelper)
    {
    }

    protected function redirectToReferer(array $mapping, string $route, array $parameters =  [ ], array $fallbackParameters = [ ]): Response {
        return $this->redirect($this->redirectHelper->getRefererPathFromQuery($mapping, $route, $parameters, $fallbackParameters));
    }

    protected function redirectToRequestReferer(string $route, array $parameters = [ ]): Response {
        return $this->redirect($this->redirectHelper->getRefererPathFromRequest($route, $parameters));
    }
}
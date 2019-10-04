<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class RefererHelper {

    private const RefQueryName = 'ref';

    private $requestStack;
    private $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router) {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function getRefererPathFromQuery(array $mapping, string $route, array $parameters = [ ], array $fallbackParameters = [ ]): string {
        $request = $this->requestStack->getMasterRequest();
        $referer = $request->query->get(static::RefQueryName, null);

        if(isset($mapping[$referer])) {
            $route = $mapping[$referer];
        } else {
            $parameters = $fallbackParameters;
        }

        return $this->router->generate($route, $parameters);
    }

    public function getRefererPathFromRequest(string $fallbackRoute, array $fallbackParameters = [ ]): string {
        $request = $this->requestStack->getMasterRequest();
        $referer = $request->headers->get('referer');

        if($referer === null) {
            return $this->router->generate($fallbackRoute, $fallbackParameters);
        }

        $baseUrl = $request->getSchemeAndHttpHost();
        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        $params = $this->router->getMatcher()->match($lastPath);

        $parameters = array_filter($params, function($key) {
            return substr($key, 0, 1) !== '_';
        }, ARRAY_FILTER_USE_KEY);

        return $this->router->generate($params['_route'], $parameters);
    }
}
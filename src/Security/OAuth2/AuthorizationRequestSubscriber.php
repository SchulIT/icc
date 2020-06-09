<?php

namespace App\Security\OAuth2;

use App\Repository\OAuthClientInfoRepositoryInterface;
use Nyholm\Psr7\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;
use Twig\Environment;

class AuthorizationRequestSubscriber implements EventSubscriberInterface {

    private $requestStack;
    private $csrfTokenManager;
    private $twig;
    private $clientInfoRepository;

    public function __construct(RequestStack $requestStack, CsrfTokenManagerInterface $csrfTokenManager, Environment $twig, OAuthClientInfoRepositoryInterface $clientInfoRepository) {
        $this->requestStack = $requestStack;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->twig = $twig;
        $this->clientInfoRepository = $clientInfoRepository;
    }

    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event) {
        $request = $this->requestStack->getMasterRequest();

        $csrfToken = new CsrfToken('oauth_authorize', $request->request->get('_csrf_token'));

        if($request->isMethod('POST') && $this->csrfTokenManager->isTokenValid($csrfToken)) {
            if ($request->request->has('authorize')) {
                $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
            } else {
                $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_DENIED);
            }
        } else {
            $response = new Response(
                200,
                [],
                $this->twig->render('oauth2/authorize.html.twig', [
                    'client' => $event->getClient(),
                    'info' => $this->clientInfoRepository->findOneByClient($event->getClient()),
                    'scopes' => $event->getScopes()
                ])
            );

            $event->setResponse($response);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE => [
                [ 'onAuthorizationRequestResolve', 10 ]
            ]
        ];
    }
}
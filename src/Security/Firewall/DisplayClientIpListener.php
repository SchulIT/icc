<?php

namespace App\Security\Firewall;

use App\Settings\DisplaySettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class DisplayClientIpListener implements EventSubscriberInterface {

    private DisplaySettings $settings;

    public function __construct(DisplaySettings $settings) {
        $this->settings = $settings;
    }

    public function onKernelRequest(RequestEvent $event) {
        if($event->isMainRequest() !== true) {
            return;
        }

        $request = $event->getRequest();

        if($request->attributes->get('_route') !== 'show_display') {
            return;
        }

        $allowedIps = $this->settings->getAllowedIpAddresses();

        if($allowedIps === null || empty($allowedIps)) {
            return;
        }

        $ips = explode(',', $allowedIps);
        if(IpUtils::checkIp($event->getRequest()->getClientIp(), $ips)) {
            return;
        }

        throw new AccessDeniedHttpException();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::REQUEST => [
                [ 'onKernelRequest', 0 ]
            ]
        ];
    }
}
<?php

namespace App\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetPhpBinarySubscriber implements EventSubscriberInterface {

    private function setPhpBinary(): void {
        $binary = $_ENV['PHP_BINARY'];

        if($binary !== null && $binary !== false) {
            putenv('PHP_BINARY=' . $binary);
        }
    }

    public function onCommand(ConsoleCommandEvent $commandEvent): void {
        $this->setPhpBinary();
    }

    public function onKernelRequest(RequestEvent $requestEvent): void {
        $this->setPhpBinary();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            ConsoleEvents::COMMAND => 'onCommand'
        ];
    }
}
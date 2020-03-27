<?php

namespace App\EventListener;

use LightSaml\Error\LightSamlException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Twig\Environment;

class LightSamlExceptionListener implements EventSubscriberInterface {

    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event) {
        $exception = $event->getThrowable();

        if($exception instanceof LightSamlException) {
            $response = new Response(
                $this->twig->render('auth/lightsaml_error.html.twig', [
                    'type' => get_class($exception),
                    'message' => $exception->getMessage()
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
            ExceptionEvent::class => 'onKernelException'
        ];
    }
}
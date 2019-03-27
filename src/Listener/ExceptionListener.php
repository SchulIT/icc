<?php

namespace App\Listener;

use App\Request\BadRequestException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface {

    private $serializer;
    private $context;

    public function __construct(SerializerInterface $serializer, SerializationContext $context) {
        $this->serializer = $serializer;
        $this->context = $context;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) {
        if(!$event->isMasterRequest()) {
            return;
        }

        $exception = $event->getException();

        if(!$exception instanceof BadRequestException) {
            return;
        }

        $json = $this->serializer->serialize($exception->getResponse(), 'json', $this->context);

        $response = new Response();
        $response->setStatusCode($exception->getCode());
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($json);

        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 10]
            ]
        ];
    }
}
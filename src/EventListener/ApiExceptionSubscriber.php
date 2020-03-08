<?php

namespace App\EventListener;

use App\Import\ValidationFailedException;
use App\Request\BadRequestException;
use App\Response\ErrorResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface {

    private const JsonContentType = 'application/json';

    private $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event) {
        $request = $event->getRequest();

        if(!in_array(static::JsonContentType, $request->getAcceptableContentTypes())) {
            return;
        }

        $throwable = $event->getThrowable();

        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = new ErrorResponse('An unknown error occured.');

        // Case 1: general HttpException (Authorization/Authentication) or BadRequest
        if($throwable instanceof HttpException || $throwable instanceof BadRequestException) {
            $code = $throwable->getStatusCode();
            $message = new ErrorResponse($throwable->getMessage());
        } else if($throwable instanceof ValidationFailedException) { // Case 2: validation failed
            $code = Response::HTTP_BAD_REQUEST;
            $message = new ErrorResponse($throwable->getMessage());
            $message->setData($throwable->getViolations());
        } else { // Case 3: General error
            $message = $throwable->getMessage();
        }

        $validStatusCodes = array_keys(Response::$statusTexts);
        if(!in_array($code, $validStatusCodes)) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new Response(
            $this->serializer->serialize($message, 'json'),
            $code
        );

        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}
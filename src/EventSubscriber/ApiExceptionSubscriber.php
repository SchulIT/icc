<?php

namespace App\EventSubscriber;

use App\Request\ValidationFailedException;
use App\Response\ErrorResponse;
use App\Response\Violation;
use App\Response\ViolationList;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface {

    private const JsonContentType = 'application/json';

    private $serializer;
    private $logger;

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger) {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event) {
        $request = $event->getRequest();

        if(!in_array(static::JsonContentType, $request->getAcceptableContentTypes())) {
            return;
        }

        $throwable = $event->getThrowable();

        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $this->logger->error('An uncaught exception was thrown.', [
            'exception' => $throwable
        ]);

        // Case 1: general HttpException (Authorization/Authentication) or BadRequest
        if($throwable instanceof HttpException) {
            $code = $throwable->getStatusCode();
            $message = new ErrorResponse($throwable->getMessage(), get_class($throwable));
        } else if($throwable instanceof ValidationFailedException) { // Case 2: validation failed
            $code = Response::HTTP_BAD_REQUEST;

            $violations = [ ];
            foreach($throwable->getViolations() as $violation) {
                $violations[] = new Violation($violation->getPropertyPath(), (string)$violation->getMessage());
            }

            $message = new ViolationList($violations);
        } else { // Case 3: General error
            $message = new ErrorResponse(
                !empty($throwable->getMessage()) ? $throwable->getMessage() : 'An unknown error occured.',
                get_class($throwable)
            );
        }

        $validStatusCodes = array_keys(Response::$statusTexts);
        if(!in_array($code, $validStatusCodes)) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new Response(
            $message !== null ? $this->serializer->serialize($message, 'json') : null,
            $code,
            [
                'Content-Type' => 'application/json'
            ]
        );

        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::EXCEPTION => [
                [ 'onKernelException', 10 ]
            ]
        ];
    }
}
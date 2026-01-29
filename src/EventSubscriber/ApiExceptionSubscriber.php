<?php

namespace App\EventSubscriber;

use App\Import\ImportException;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class ApiExceptionSubscriber implements EventSubscriberInterface {

    private const JsonContentType = 'application/json';

    public function __construct(private readonly SerializerInterface $serializer, private readonly LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void {
        $request = $event->getRequest();

        if(!in_array(self::JsonContentType, $request->getAcceptableContentTypes())) {
            return;
        }

        $throwable = $event->getThrowable();

        if($throwable instanceof AuthenticationCredentialsNotFoundException) {
            return;
        }

        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $this->logger->error($throwable->getMessage(), [
            'exception' => $throwable
        ]);

        // Case 1: general HttpException (Authorization/Authentication) or BadRequest
        if($throwable instanceof HttpException) {
            $code = $throwable->getStatusCode();
            $message = new ErrorResponse($throwable->getMessage(), $throwable::class);
        } else if($throwable instanceof AccessDeniedException) {
            $code = Response::HTTP_FORBIDDEN;
            $message = new ErrorResponse($throwable->getMessage(), $throwable::class);
        } else if($throwable instanceof ValidationFailedException) { // Case 2: validation failed
            $code = Response::HTTP_BAD_REQUEST;

            $violations = [ ];
            foreach($throwable->getViolations() as $violation) {
                $violations[] = new Violation($violation->getPropertyPath(), (string)$violation->getMessage());
            }

            $message = new ViolationList($violations);
        } else if($throwable instanceof ImportException) {
            $code = Response::HTTP_BAD_REQUEST;

            $message = new ErrorResponse(
                $throwable->getMessage(),
                $throwable::class
            );
        } else { // Case 3: General error
            $message = new ErrorResponse(
                'An unknown error occured.',
                $throwable::class
            );
        }

        $validStatusCodes = array_keys(Response::$statusTexts);
        if(!in_array($code, $validStatusCodes)) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new Response(
            $this->serializer->serialize($message, 'json'),
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
    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::EXCEPTION => [
                [ 'onKernelException', 10 ]
            ]
        ];
    }
}
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/auth')]
class AuthenticationController extends AbstractController {

    #[Route(path: '/error')]
    public function error(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response {
        $exception = $authenticationUtils->getLastAuthenticationError();

        if($exception === null) {
            return $this->redirectToRoute('lightsaml_sp.login');
        }

        if($exception->getPrevious() !== null) {
            $exception = $exception->getPrevious();
        }

        $exceptionType = $exception::class;

        if(method_exists($exception, 'getMessageKey')) {
            $messageKey = $exception->getMessageKey();
            $message = $translator->trans($messageKey, [], 'security');
        } else {
            $message = $exception->getMessage();
        }

        return $this->render('error/auth.html.twig', [
            'exception' => $exception,
            'type' => $exceptionType,
            'message' => $message
        ]);
    }
}
<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/auth")
 */
class AuthenticationController extends AbstractController {

    /**
     * @Route("/error")
     */
    public function error(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator) {
        $exception = $authenticationUtils->getLastAuthenticationError();

        if($exception === null) {
            return $this->redirectToRoute('lightsaml_sp.login');
        }

        $exceptionType = get_class($exception);
        $messageKey = $exception->getMessageKey();

        $message = $translator->trans($messageKey, [], 'security');

        return $this->render('error/auth.html.twig', [
            'exception' => $exception,
            'type' => $exceptionType,
            'message' => $message
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageScope;
use App\Message\DismissedMessagesHelper;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\CurrentUserResolver;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractControllerWithMessages {

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, MessageRepositoryInterface $messageRepository,
                                DismissedMessagesHelper $dismissedMessagesHelper, CurrentUserResolver $userResolver,
                                DateHelper $dateHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $userResolver, $dateHelper);

        $this->userRepository = $userRepository;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Messages();
    }

    /**
     * @Route("/messages/{id}/dismiss", name="dismiss_message")
     */
    public function dismiss(Message $message, Request $request) {
        $user = $this->userResolver->getUser();

        if($user->getDismissedMessages()->contains($message) !== true) {
            $user->addDismissedMessage($message);
            $this->userRepository->persist($user);
        }

        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/messages/{id}/reenable", name="reenable_message")
     */
    public function reenable(Message $message, Request $request) {
        $user = $this->userResolver->getUser();

        if($user->getDismissedMessages()->contains($message) === true) {
            $user->removeDismissedMessage($message);
            $this->userRepository->persist($user);
        }

        return $this->redirectToReferer($request);
    }

    private function redirectToReferer(Request $request): Response {
        $referer = $request->headers->get('referer');

        if($referer === null) {
            return $this->redirectToRoute('dashboard');
        }

        $baseUrl = $request->getSchemeAndHttpHost();
        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        $params = $this->get('router')->getMatcher()->match($lastPath);

        $parameters = array_filter($params, function($key) {
            return substr($key, 0, 1) !== '_';
        }, ARRAY_FILTER_USE_KEY);

        return $this->redirectToRoute($params['_route'], $parameters);
    }

}
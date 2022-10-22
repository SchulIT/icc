<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\User;
use App\Message\DismissedMessagesHelper;
use App\Repository\MessageRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerWithMessages extends AbstractController {
    protected $userResolver;

    public function __construct(protected MessageRepositoryInterface $messageRepository, protected DismissedMessagesHelper $dismissedMessagesHelper,
                                 protected DateHelper $dateHelper, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    protected abstract function getMessageScope(): MessageScope;

    public function renderWithMessages(string $view, array $parameters = [], Response $response = null): Response {
        /** @var User $user */
        $user = $this->getUser();

        if($user !== null) {
            $messages = $this->messageRepository->findBy(
                $this->getMessageScope(),
                $user->getUserType(),
                $this->dateHelper->getToday()
            );

            $parameters['_messages'] = $this->dismissedMessagesHelper->getNonDismissedMessages($messages, $user);
            $parameters['_dismissed_messages'] = $this->dismissedMessagesHelper->getDismissedMessages($messages, $user);
        }

        return parent::render($view, $parameters, $response);
    }
}
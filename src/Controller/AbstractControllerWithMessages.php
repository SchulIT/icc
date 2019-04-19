<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Message\DismissedMessagesHelper;
use App\Repository\MessageRepositoryInterface;
use App\Security\CurrentUserResolver;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerWithMessages extends AbstractController {
    protected $messageRepository;
    protected $dismissedMessagesHelper;
    protected $userResolver;
    protected $dateHelper;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                CurrentUserResolver $userResolver, DateHelper $dateHelper) {
        $this->messageRepository = $messageRepository;
        $this->dismissedMessagesHelper = $dismissedMessagesHelper;
        $this->userResolver = $userResolver;
        $this->dateHelper = $dateHelper;
    }

    protected abstract function getMessageScope(): MessageScope;

    public function render(string $view, array $parameters = [], Response $response = null): Response {
        $user = $this->userResolver->getUser();

        if($user !== null) {
            $messages = $this->messageRepository->findBy(
                $this->getMessageScope(),
                $user->getUserType(),
                $this->dateHelper->getToday()
            );

            dump(count($messages));

            $parameters['_messages'] = $this->dismissedMessagesHelper->getNonDismissedMessages($messages, $user);
            $parameters['_dismissedMessages'] = $this->dismissedMessagesHelper->getDismissedMessages($messages, $user);
        }

        return parent::render($view, $parameters, $response);
    }
}
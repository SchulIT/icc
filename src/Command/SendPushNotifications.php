<?php

namespace App\Command;

use App\Event\MessageCreatedEvent;
use App\Notification\NotificationService;
use App\Repository\MessageRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/5 * * * *")
 */
class SendPushNotifications extends Command {

    private DateHelper $dateHelper;
    private NotificationService $notificationService;
    private MessageRepositoryInterface $messageRepository;

    public function __construct(DateHelper $dateHelper, NotificationService $notificationService, MessageRepositoryInterface $messageRepository, string $name = null) {
        parent::__construct($name);

        $this->dateHelper = $dateHelper;
        $this->notificationService = $notificationService;
        $this->messageRepository = $messageRepository;
    }

    public function configure() {
        $this->setName('app:notifications:send')
            ->setDescription('Sends notifications for messages which did not push any notification yet.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $today = $this->dateHelper->getToday();
        $messages = $this->messageRepository->findAllNotificationNotSent($today);

        if(count($messages) > 0) {
            $message = $messages[0];
            $style->section(sprintf('Send notifications for message "%s"', $message->getTitle()));

            $this->notificationService->sendNotifications(new MessageCreatedEvent($message));
            $style->success(sprintf('Done (%d still queued for sending notifications)', count($messages) - 1));
        } else {
            $style->success('No messages found with unsent notifications.');
        }

        return 0;
    }
}
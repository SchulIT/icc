<?php

namespace App\Command;

use App\Notification\WebPush\PushNotificationService;
use App\Notification\WebPush\UserSubscriptionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TriggerNotificationCommand extends Command {

    private $senderSubscriber;

    public function __construct(PushNotificationService $subscriptionManager, string $name = null) {
        parent::__construct($name);
        $this->senderSubscriber = $subscriptionManager;
    }

    public function configure() {
        $this->setName('push:web:trigger')
            ->setDescription('Triggers a web push notification.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $this->senderSubscriber->sendNotifications();
    }
}
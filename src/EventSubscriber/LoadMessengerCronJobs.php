<?php

namespace App\EventSubscriber;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

class LoadMessengerCronJobs implements EventSubscriberInterface {

    public function __construct(private readonly bool $useCronjobForMessenger, private readonly ConsumeMessagesCommand $command) {

    }

    public function onLoadJobs(LoadJobsEvent $event): void {
        dump($this->useCronjobForMessenger);

        if(!$this->useCronjobForMessenger) {
            return;
        }

        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'async -vv --time-limit=20 --no-reset'));
        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'mail -vv --time-limit=20 --no-reset'));
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            LoadJobsEvent::class => 'onLoadJobs',
        ];
    }
}
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;

class WorkerRunningSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly bool $useCronjobForMessenger) {

    }

    public function onWorkerRunning(WorkerRunningEvent $runningEvent): void {
        if(!$this->useCronjobForMessenger) {
            return;
        }

        if($runningEvent->isWorkerIdle()) {
            $runningEvent->getWorker()->stop();
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning'
        ];
    }
}
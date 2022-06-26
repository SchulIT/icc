<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;

class WorkerRunningSubscriber implements EventSubscriberInterface {
    public function onWorkerRunning(WorkerRunningEvent $runningEvent) {
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
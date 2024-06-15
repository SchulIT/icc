<?php

namespace App\EventSubscriber;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This collected dispatched events coming from Doctrine event listeners. Problem with
 * Doctrine listeners: persisting any other entity inside any of the events is not
 * allowed.
 *
 * Instead of dispatching events to the EventDispatcher directly, we collect them here
 * and fire them after the request has been terminated. Persisting anything else (like
 * notifications) is safe then :)
 *
 * Inspiration: https://romaricdrigon.github.io/2019/08/09/domain-events
 */
class DoctrineEventsCollector implements EventSubscriberInterface {

    private array $collectedEvents = [ ];

    public function __construct(private readonly EventDispatcherInterface $dispatcher) {

    }

    public function collect(Event $event): void {
        $this->collectedEvents[] = $event;
    }

    private function dispatchAllEvents(): void {
        foreach($this->collectedEvents as $event) {
            $this->dispatcher->dispatch($event);
        }
    }

    public function onKernelTerminate(TerminateEvent $event): void {
        $this->dispatchAllEvents();
    }

    public function onConsoleTermine(ConsoleTerminateEvent $event): void {
        $this->dispatchAllEvents();
    }

    public static function getSubscribedEvents(): array {
        return [
            TerminateEvent::class => 'onKernelTerminate',
            ConsoleTerminateEvent::class => 'onConsoleTermine'
        ];
    }
}
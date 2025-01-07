<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

/**
 * This collector collects entities to be persisted into the database. This
 * is useful for entities that need to be persisted during a Doctrine event
 * listener.
 *
 * The documentation states that using EntityManager::flush() is highly
 * discouraged during any Doctrine listener.
 */
class DoctrineEntityCollector implements EventSubscriberInterface {

    private array $collectedForPersist = [ ];

    private array $collectedForRemoval = [ ];

    public function __construct(private readonly EntityManagerInterface $em) {

    }

    public function collectForPersist(object $entity): void {
        $this->collectedForPersist[] = $entity;
    }

    public function collectForRemoval(object $entity): void {
        $this->collectedForRemoval[] = $entity;
    }

    private function persistRemoveAndFlush(): void {
        if(empty($this->collectedForPersist) && empty($this->collectedForRemoval)) {
            /*
             * Prevent calling flush (at the bottom of this method) due to a
             * possible bug. Maybe there is a problem with the tree hydration mode
             * (gedmo/doctrine-extensions) which is causing a change in
             * WikiArticle::parent property (?)
             */
            return;
        }

        foreach($this->collectedForPersist as $entity) {
            $this->em->persist($entity);
        }

        foreach($this->collectedForRemoval as $entity) {
            $this->em->remove($entity);
        }

        $this->em->flush();
    }

    public function onKernelTerminate(TerminateEvent $event): void {
        $this->persistRemoveAndFlush();
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void {
        $this->persistRemoveAndFlush();
    }

    public static function getSubscribedEvents(): array {
        return [
            TerminateEvent::class => ['onKernelTerminate', 10],
            ConsoleTerminateEvent::class => ['onConsoleTerminate', 10]
        ];
    }
}
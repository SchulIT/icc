<?php

namespace App\Doctrine;

use App\Entity\TeacherAbsence;
use App\Event\TeacherAbsenceCreatedEvent;
use App\Event\TeacherAbsenceUpdatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
class TeacherAbsencePersistSubscriber {

    public function __construct(private readonly EventDispatcherInterface $dispatcher) { }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof TeacherAbsence) {
            $this->dispatcher->dispatch(new TeacherAbsenceCreatedEvent($entity));
        }
    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof TeacherAbsence) {
            $changeset = $eventArgs->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);
            $ignoreProperties = [ 'processedAt', 'processedBy' ];

            foreach($ignoreProperties as $property) {
                unset($changeset[$property]);
            }

            if(count($changeset) > 0) {
                $this->dispatcher->dispatch(new TeacherAbsenceUpdatedEvent($entity));
            }
        }
    }
}
<?php

namespace App\Doctrine;

use App\Entity\Attendance;
use App\Messenger\RunIntegrityCheckMessage;
use App\Section\SectionResolverInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postUpdate)]
class AttendanceUpdateSubscriber {
    public function __construct(private readonly bool $isEnabled, private readonly MessageBusInterface $messageBus, private readonly SectionResolverInterface $sectionResolver) { }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        if($this->isEnabled === false) {
            return;
        }

        $entity = $eventArgs->getObject();

        if(!$entity instanceof Attendance) {
            return;
        }

        $date = null;

        if($entity->getEntry() !== null) {
            $date = $entity->getEntry()->getLesson()->getDate();
        } else if($entity->getEvent() !== null) {
            $date = $entity->getEvent()->getDate();
        }

        if($date !== null) {
            $section = $this->sectionResolver->getSectionForDate($date);
            $this->messageBus->dispatch(new RunIntegrityCheckMessage($entity->getStudent()->getId(), $section->getStart(), $section->getEnd()));
        }
    }
}
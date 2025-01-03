<?php

namespace App\Tests\Doctrine;

use App\Doctrine\SubstitutionMentionSubscriber;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Event\SubstitutionMentionCreatedEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use App\Repository\TeacherRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;

class SubstitutionMentionSubscriberTest extends TestCase {

    private function createTeacherRepository(): TeacherRepositoryInterface {
        $repository = $this->createMock(TeacherRepositoryInterface::class);

        $repository
            ->method('findAll')
            ->willReturn([
                (new Teacher())->setAcronym('ABCD'),
                (new Teacher())->setAcronym('WXYZ'),
            ]);

        return $repository;
    }

    public function testSubstitutionWithEmptyRemarkDoesNotFail(): void {
        $substitution = (new Substitution())
            ->setRemark('Aufgaben');

        $collector = $this->createMock(DoctrineEventsCollector::class);
        $collector
            ->expects($this->never())
            ->method('collect');

        $subscriber = new SubstitutionMentionSubscriber($this->createTeacherRepository(), $collector);
        $subscriber->prePersist(new PrePersistEventArgs($substitution, $this->createMock(EntityManagerInterface::class)));
    }

    public function testSubstitutionHasMentionAndTriggersEvent(): void {
        $substitution = (new Substitution())
            ->setRemark('Aufgaben ABCD');

        $collector = $this->createMock(DoctrineEventsCollector::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->will($this->returnCallback(function(SubstitutionMentionCreatedEvent $event) use ($substitution) {
                $this->assertEquals($substitution, $event->getSubstitution());
                $this->assertEquals('ABCD', $event->getTeacher()->getAcronym());
            }));

        $subscriber = new SubstitutionMentionSubscriber($this->createTeacherRepository(), $collector);
        $subscriber->prePersist(new PrePersistEventArgs($substitution, $this->createMock(EntityManagerInterface::class)));
    }

    public function testSubstitutionHasNoMentionAndDoesNotTriggerEvent(): void {
        $substitution = (new Substitution())
            ->setRemark('Aufgaben BCDE');

        $collector = $this->createMock(DoctrineEventsCollector::class);
        $collector
            ->expects($this->never())
            ->method('collect');

        $subscriber = new SubstitutionMentionSubscriber($this->createTeacherRepository(), $collector);
        $subscriber->prePersist(new PrePersistEventArgs($substitution, $this->createMock(EntityManagerInterface::class)));
    }
}
<?php

namespace App\Tests\Doctrine;

use App\Substitution\Doctrine\SubstitutionMentionSubscriber;
use App\Substitution\Entity\Substitution;
use App\Common\Entity\Teacher;
use App\Substitution\Event\SubstitutionMentionCreatedEvent;
use App\Infrastructure\EventSubscriber\DoctrineEventsCollector;
use App\Common\Repository\TeacherRepositoryInterface;
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
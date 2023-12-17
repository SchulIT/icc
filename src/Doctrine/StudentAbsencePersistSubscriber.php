<?php

namespace App\Doctrine;

use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceMessage;
use App\Event\StudentAbsenceCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
class StudentAbsencePersistSubscriber {

    private const FromDateProperty = 'from.date';
    private const FromLessonProperty = 'from.lesson';
    private const UntilDateProperty = 'until.date';
    private const UntilLessonProperty = 'until.lesson';

    public function __construct(private readonly EventDispatcherInterface $dispatcher, private readonly TranslatorInterface $translator)
    {
    }

    public function postUpdate(PostUpdateEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof StudentAbsence) {
            $changeset = $eventArgs->getObjectManager()->getUnitOfWork()
                ->getEntityChangeSet($entity);

            // Step 1: fix the fact that the date object seems to marked as "changed"
            if(array_key_exists(self::FromDateProperty, $changeset) && $changeset[self::FromDateProperty][0] == $changeset[self::FromDateProperty][1]) {
                unset($changeset[self::FromDateProperty]);
            }

            if(array_key_exists(self::UntilDateProperty, $changeset) && $changeset[self::UntilDateProperty][0] == $changeset[self::UntilDateProperty][1]) {
                unset($changeset[self::UntilDateProperty]);
            }

            $message = (new StudentAbsenceMessage())
                ->setAbsence($entity);
            $messages = [ ];

            // Step 2: detect change
            if(array_key_exists(self::FromDateProperty, $changeset) || array_key_exists(self::FromLessonProperty, $changeset)) {
                $oldDate = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getDate(), $changeset[self::FromDateProperty] ?? null, 0);
                $newDate = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getDate(), $changeset[self::FromDateProperty] ?? null, 1);;
                $oldLesson = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getLesson(), $changeset[self::FromLessonProperty] ?? null, 0);
                $newLesson = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getLesson(), $changeset[self::FromLessonProperty] ?? null, 1);
                $translationKey = 'absences.students.edit.from_date';

                $messages[] = $this->translator->trans($translationKey, [
                    '%oldDate%' => $oldDate->format($this->translator->trans('date.format')),
                    '%newDate%' => $newDate->format($this->translator->trans('date.format')),
                    '%oldLesson%' => $oldLesson,
                    '%newLesson%' => $newLesson
                ]);
            }

            if(array_key_exists(self::UntilDateProperty, $changeset) || array_key_exists(self::UntilLessonProperty, $changeset)) {
                $oldDate = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getDate(), $changeset[self::UntilDateProperty] ?? null, 0);
                $newDate = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getDate(), $changeset[self::UntilDateProperty] ?? null, 1);;
                $oldLesson = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getLesson(), $changeset[self::UntilLessonProperty] ?? null, 0);
                $newLesson = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getLesson(), $changeset[self::UntilLessonProperty] ?? null, 1);
                $translationKey = 'absences.students.edit.until_date';

                $messages[] = $this->translator->trans($translationKey, [
                    '%oldDate%' => $oldDate->format($this->translator->trans('date.format')),
                    '%newDate%' => $newDate->format($this->translator->trans('date.format')),
                    '%oldLesson%' => $oldLesson,
                    '%newLesson%' => $newLesson
                ]);
            }

            if(count($messages) === 0) {
                return;
            }

            $message->setMessage(implode(' ', $messages));

            $eventArgs->getObjectManager()->persist($message);
            $eventArgs->getObjectManager()->flush();
        }
    }

    private function getValueFromChangesetOrEntityValue(mixed $entityValue, ?array $changeset, int $index) {
        if($changeset === null) {
            return $entityValue;
        }

        return $changeset[$index];
    }

    public function postPersist(PostPersistEventArgs $eventArgs): void {
        $entity = $eventArgs->getObject();

        if($entity instanceof StudentAbsence) {
            $this->dispatcher->dispatch(new StudentAbsenceCreatedEvent($entity));
        }
    }
}
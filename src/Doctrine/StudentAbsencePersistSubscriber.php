<?php

namespace App\Doctrine;

use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceMessage;
use App\Event\StudentAbsenceCreatedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentAbsencePersistSubscriber implements EventSubscriber {

    private const FromDateProperty = 'from.date';
    private const FromLessonProperty = 'from.lesson';
    private const UntilDateProperty = 'until.date';
    private const UntilLessonProperty = 'until.lesson';

    public function __construct(private EventDispatcherInterface $dispatcher, private TranslatorInterface $translator)
    {
    }

    public function postUpdate(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof StudentAbsence) {
            $changeset = $eventArgs->getEntityManager()->getUnitOfWork()
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
            $translationKey = null;

            $oldDate = null;
            $newDate = null;
            $oldLesson = 0;
            $newLesson = 0;

            // Step 2: detect change
            if(array_key_exists(self::FromDateProperty, $changeset) || array_key_exists(self::FromLessonProperty, $changeset)) {
                $oldDate = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getDate(), $changeset[self::FromDateProperty] ?? null, 0);
                $newDate = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getDate(), $changeset[self::FromDateProperty] ?? null, 1);;
                $oldLesson = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getLesson(), $changeset[self::FromLessonProperty] ?? null, 0);
                $newLesson = $this->getValueFromChangesetOrEntityValue($entity->getFrom()->getLesson(), $changeset[self::FromLessonProperty] ?? null, 1);
                $translationKey = 'absences.students.edit.from_date';
            }

            if(array_key_exists(self::UntilDateProperty, $changeset) || array_key_exists(self::UntilLessonProperty, $changeset)) {
                $oldDate = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getDate(), $changeset[self::UntilDateProperty] ?? null, 0);
                $newDate = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getDate(), $changeset[self::UntilDateProperty] ?? null, 1);;
                $oldLesson = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getLesson(), $changeset[self::UntilLessonProperty] ?? null, 0);
                $newLesson = $this->getValueFromChangesetOrEntityValue($entity->getUntil()->getLesson(), $changeset[self::UntilLessonProperty] ?? null, 1);
                $translationKey = 'absences.students.edit.until_date';
            }

            if($translationKey === null) {
                return;
            }

            $message->setMessage(
                $this->translator->trans($translationKey, [
                    '%oldDate%' => $oldDate->format($this->translator->trans('date.format')),
                    '%newDate%' => $newDate->format($this->translator->trans('date.format')),
                    '%oldLesson%' => $oldLesson,
                    '%newLesson%' => $newLesson
                ])
            );

            $eventArgs->getEntityManager()->persist($message);
            $eventArgs->getEntityManager()->flush();
        }
    }

    private function getValueFromChangesetOrEntityValue(mixed $entityValue, ?array $changeset, int $index) {
        if($changeset === null) {
            return $entityValue;
        }

        return $changeset[$index];
    }

    public function postPersist(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof StudentAbsence) {
            $this->dispatcher->dispatch(new StudentAbsenceCreatedEvent($entity));
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::postPersist,
            Events::postUpdate
        ];
    }
}
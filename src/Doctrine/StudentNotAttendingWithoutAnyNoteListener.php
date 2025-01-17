<?php

namespace App\Doctrine;

use App\Book\AttendanceSuggestion\PreviousAbsenceSuggestionStrategy;
use App\Book\AttendanceSuggestion\SuggestionResolver;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Event\StudentAbsentWithoutAnyNoteEvent;
use App\EventSubscriber\DoctrineEventsCollector;
use App\Response\Book\AttendanceSuggestion;
use App\Utils\ArrayUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
class StudentNotAttendingWithoutAnyNoteListener {

    private const array ExcludedStrategies = [
        PreviousAbsenceSuggestionStrategy::class
    ];

    private array $cache = [ ];

    public function __construct(private readonly SuggestionResolver $suggestionResolver, private readonly DoctrineEventsCollector $eventsCollector) {

    }

    public function postPersist(PostPersistEventArgs $event): void {
        $attendance = $event->getObject();

        if(!$attendance instanceof Attendance) {
            return;
        }

        if($attendance->getType() !== AttendanceType::Absent) {
            return;
        }

        $entry = $attendance->getEntry();

        if($entry === null) {
            return;
        }

        if(!array_key_exists($entry->getId(), $this->cache)) {
            $this->cache[$entry->getId()] = $this->suggestionResolver->resolve($entry->getTuition(), $entry->getLesson()->getDate(), $attendance->getLesson(), $attendance->getLesson(), self::ExcludedStrategies);
        }

        /** @var AttendanceSuggestion[] $suggestions */
        $suggestions = $this->cache[$entry->getId()];

        /** @var AttendanceSuggestion|null $suggestion */
        $suggestion = ArrayUtils::first($suggestions, fn(AttendanceSuggestion $suggestion): bool => $suggestion->getStudent()->getUuid() === $attendance->getStudent()->getUuid()->toString());

        if($suggestion !== null && $suggestion->getAttendanceType() === AttendanceType::Absent->value) {
            return;
        }

        $this->eventsCollector->collect(new StudentAbsentWithoutAnyNoteEvent($attendance));
    }
}
<?php

namespace App\Book\EventListener;

use App\Book\Repository\LessonAttendanceRepositoryInterface;
use App\Common\Import\Event\StudentRemoveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class RemoveStudentOnlyIfNoAttendancesAreInDatabase  {
    public function __construct(
        private LessonAttendanceRepositoryInterface $lessonAttendanceRepository
    ) { }

    public function __invoke(StudentRemoveEvent $event): void {
        if($event->preventRemoval === true) {
            return; // just for performance improvements
        }

        $count = $this->lessonAttendanceRepository->countAnyByStudent($event->entity);

        if($count > 0) {
            $event->preventRemoval = true;
        }
    }
}

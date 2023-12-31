<?php

namespace App\Messenger;

use App\TeacherAbsence\TimetableLessonResolver;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ResolveTimetableLessonsForAbsenceLessonHandler {

    public function __construct(private readonly TimetableLessonResolver $resolver) {

    }


    public function __invoke(ResolveTimetableLessonsForAbsenceLessonMessage $message): void {
        $this->resolver->resolve($message->getStartDate(), $message->getEndDate());
    }
}
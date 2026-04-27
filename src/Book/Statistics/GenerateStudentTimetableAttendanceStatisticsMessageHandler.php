<?php

namespace App\Book\Statistics;

use App\Repository\StudentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateStudentTimetableAttendanceStatisticsMessageHandler {

    public function __construct(
        private StudentTimetableAttendanceStatisticsGenerator $generator,
        private StudentRepositoryInterface $studentRepository
    ) {

    }

    public function __invoke(GenerateStudentTimetableAttendanceStatisticsMessage $message): void {
        $student = $this->studentRepository->findOneById($message->studentId);

        if($student === null) {
            return;
        }

        $this->generator->regenerate($student, $message->start, $message->end);
    }
}
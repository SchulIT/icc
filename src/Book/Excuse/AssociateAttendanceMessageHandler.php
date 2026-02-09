<?php

namespace App\Book\Excuse;

use App\Repository\LessonAttendanceRepositoryInterface;
use phpDocumentor\Reflection\PseudoTypes\IntegerValue;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class AssociateAttendanceMessageHandler {

    public function __construct(
        private ExcuseNoteAssociator $associator,
        private LessonAttendanceRepositoryInterface $attendanceRepository
    ) {

    }

    public function __invoke(AssociateAttendanceMessage $message): void {
        $attendance = $this->attendanceRepository->findOneById($message->attendanceId);

        if($attendance === null) {
            return;
        }

        $this->associator->associateAttendance($attendance);
    }
}
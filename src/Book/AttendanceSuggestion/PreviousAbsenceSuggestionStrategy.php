<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Entity\AttendanceType;
use App\Entity\Tuition;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class PreviousAbsenceSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly TranslatorInterface $translator,
                                private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [ ];

        foreach($this->attendanceRepository->findAbsentByStudentsAndDate($students, $date) as $attendance) {
            if($attendance->getEntry()->getLessonEnd() === $lessonStart - 1 && $attendance->isZeroAbsentLesson() === false) {
                $suggestion = new AttendanceSuggestion(
                    $this->getStudent($attendance->getStudent()),
                    $this->translator->trans('book.attendance.absence_reason.absent_before'),
                    range($lessonStart, $lessonEnd),
                    AttendanceType::Absent->value
                );

                $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForPreviouslyAbsent(), $attendance->getStudent(), $suggestion);
            }
        }

        return $suggestions;
    }
}
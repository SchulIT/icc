<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceType;
use App\Entity\Tuition;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExcuseSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly TranslatorInterface $translator,
                                private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [ ];

        foreach($this->excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            if($note->appliesToLesson($date, $lesson)) {
                $suggestion = new AttendanceSuggestion(
                    $this->getStudent($note->getStudent()),
                    $this->translator->trans('book.attendance.absence_reason.excuse'),
                    LessonAttendanceType::Absent,
                    false,
                    LessonAttendanceExcuseStatus::Excused,
                    $this->urlGenerator->generate('edit_excuse', ['uuid' => $note->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL)
                );

                $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForExcuseNote(), $note->getStudent(), $suggestion);
            }
        }

        return $suggestions;
    }
}
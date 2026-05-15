<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Book\Entity\AttendanceExcuseStatus;
use App\Book\Entity\AttendanceType;
use App\Common\Entity\Tuition;
use App\Book\Repository\ExcuseNoteRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Book\Settings\BookSettings;
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

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [ ];

        foreach($this->excuseNoteRepository->findByStudentsAndDate($students, $date) as $note) {
            $lessons = [ ];

            for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
                if($note->appliesToLesson($date, $lessonNumber)) {
                    $lessons[] = $lessonNumber;
                }
            }

            if(count($lessons) > 0) {
                $suggestion = new AttendanceSuggestion(
                    $this->getStudent($note->getStudent()),
                    $this->translator->trans('book.attendance.absence_reason.excuse'),
                    $lessons,
                    AttendanceType::Absent->value,
                    false,
                    AttendanceExcuseStatus::Excused->value,
                    $this->urlGenerator->generate('edit_excuse', ['uuid' => $note->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL)
                );

                $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForExcuseNote(), $note->getStudent(), $suggestion);
            }
        }

        return $suggestions;
    }
}
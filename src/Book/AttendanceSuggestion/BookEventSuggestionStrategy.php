<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\Tuition;
use App\Repository\BookEventRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookEventSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly BookEventRepositoryInterface $bookEventRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly BookSettings $bookSettings) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);

        $suggestions = [ ];

        foreach($students as $student) {
            foreach($this->bookEventRepository->findByStudent($student, $date, $date) as $event) {
                $lessons = [ ];

                for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
                    if($event->getLessonStart() <= $lessonNumber && $event->getLessonEnd() >= $lessonStart) {
                        $lessons[] = $lessonNumber;
                    }
                }

                if(count($lessons) === 0) {
                    continue;
                }

                $suggestion = new AttendanceSuggestion(
                    $this->getStudent($student),
                    $event->getTitle(),
                    $lessons,
                    AttendanceType::Absent->value,
                    true,
                    AttendanceExcuseStatus::NotSet->value,
                    $this->urlGenerator->generate('show_or_edit_book_event_entry', [ 'uuid' => $event->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL)
                );

                $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForBookEvent(), $student, $suggestion);
            }
        }

        return $suggestions;
    }
}
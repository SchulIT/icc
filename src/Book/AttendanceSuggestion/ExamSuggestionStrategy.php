<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Dashboard\AbsentExamStudent;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceType;
use App\Entity\Tuition;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly ExamStudentsResolver $examStudentsResolver,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly TranslatorInterface $translator,
                                private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $this->studentsResolver->resolve($tuition);
        /** @var AbsentExamStudent[] $examStudents */
        $examStudents = $this->examStudentsResolver->resolveAbsentStudents($date, $lesson, $students);

        $suggestions = [ ];

        foreach($examStudents as $examStudent) {
            if($examStudent->getExam()->getTuitions()->count() === 1 && $examStudent->getExam()->getTuitions()->first() === $tuition) {
                continue; // do not show exam of current tuition
            }

            if($examStudent->getTuition() !== null && $examStudent->getTuition()->getId() === $tuition->getId()) {
                continue; 
            }

            $suggestion = new AttendanceSuggestion(
                $this->getStudent($examStudent->getStudent()),
                $this->translator->trans('book.attendance.absence_reason.exam'),
                LessonAttendanceType::Absent,
                true,
                LessonAttendanceExcuseStatus::Excused,
                $this->urlGenerator->generate('show_exam', ['uuid' => $examStudent->getExam()->getUuid() ])
            );

            $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForExams(), $examStudent->getStudent(), $suggestion);
        }

        return $suggestions;
    }
}
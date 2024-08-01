<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Dashboard\AbsentExamStudent;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\Tuition;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use App\Utils\ArrayUtils;
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

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);

        /** @var AbsentExamStudent[] $examStudents */
        $examStudents = [ ];

        for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
            $examStudents = array_merge($examStudents, $this->examStudentsResolver->resolveAbsentStudents($date, $lessonNumber, $students));
        }

        // kill duplicates
        /** @var AbsentExamStudent[] $examStudents */
        $examStudents = ArrayUtils::unique($examStudents);
        $suggestions = [ ];

        foreach($examStudents as $examStudent) {
            if($examStudent->getExam()->getTuitions()->count() === 1 && $examStudent->getExam()->getTuitions()->first() === $tuition) {
                continue; // do not show exam of current tuition
            }

            if($examStudent->getTuition() !== null && $examStudent->getTuition()->getId() === $tuition->getId()) {
                continue; 
            }

            $lessons = [ ];
            for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
                if($examStudent->getExam()->getLessonStart() <= $lessonNumber && $examStudent->getExam()->getLessonEnd() >= $lessonNumber) {
                    $lessons[] = $lessonNumber;
                }
            }

            $suggestion = new AttendanceSuggestion(
                $this->getStudent($examStudent->getStudent()),
                $this->translator->trans('book.attendance.absence_reason.exam', ['%tuition%' => $examStudent->getTuition()?->getName()]),
                $lessons,
                AttendanceType::Absent->value,
                true,
                AttendanceExcuseStatus::Excused->value,
                $this->urlGenerator->generate('show_exam', ['uuid' => $examStudent->getExam()->getUuid() ])
            );

            $suggestions[] = new PrioritizedSuggestion($this->bookSettings->getSuggestionPriorityForExams(), $examStudent->getStudent(), $suggestion);
        }

        return $suggestions;
    }
}
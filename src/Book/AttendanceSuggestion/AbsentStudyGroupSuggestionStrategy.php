<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Entity\Absence;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\DateLesson;
use App\Entity\Tuition;
use App\Repository\AbsenceRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use App\Utils\ArrayUtils;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbsentStudyGroupSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly AbsenceRepositoryInterface $absenceRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly TranslatorInterface $translator,
                                private readonly BookSettings $bookSettings) {

    }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [];

        foreach($students as $student) {
            $absences = [ ];

            for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
                $absences = array_merge($absences, $this->absenceRepository->findAllByStudentAndDateAndLesson($student, $date, $lessonNumber));
            }

            /** @var Absence[] $absences */
            $absences = ArrayUtils::unique($absences);

            foreach($absences as $absence) {
                if($absence->getLessonStart() === null && $absence->getLessonEnd() === null) {
                    $lessons = range($lessonStart, $lessonEnd);
                } else {
                    $lessons = array_intersect(
                        range($lessonStart, $lessonEnd),
                        range($absence->getLessonStart(), $absence->getLessonEnd())
                    );
                }

                if(count($lessons) === 0) {
                    continue;
                }

                $suggestion = new AttendanceSuggestion(
                    $this->getStudent($student),
                    $this->translator->trans('book.attendance.absence_reason.absent_study_group', ['%grade%' => $student->getGrade($tuition->getSection())?->getName() ?? 'Klasse']),
                    $lessons,
                    AttendanceType::Absent->value,
                    true,
                    AttendanceExcuseStatus::Excused->value
                );

                $suggestions[] = new PrioritizedSuggestion(
                    $this->bookSettings->getSuggestionPriorityForAbsentStudyGroup(),
                    $student,
                    $suggestion,
                );
            }
        }

        return $suggestions;
    }
}
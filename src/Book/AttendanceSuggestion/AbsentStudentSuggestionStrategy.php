<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceFlag;
use App\Entity\LessonAttendanceType;
use App\Entity\StudentAbsence;
use App\Entity\Subject;
use App\Entity\Tuition;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Settings\BookSettings;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbsentStudentSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly StudentAbsenceRepositoryInterface $absenceRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lesson): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [ ];

        foreach($this->absenceRepository->findByStudents($students, null, $date, $lesson) as $absence) {
            $type = $absence->getType();
            $subjectIds = array_map(fn(Subject $subject) => $subject->getId(), $type->getSubjects()->toArray());

            if(count($subjectIds) > 0 && !in_array($tuition->getSubject()->getId(), $subjectIds)) {
                // Ignore absence in case the type is bound to any subject and the subject of the given tuition does not match
                continue;
            }

            $suggestion = new AttendanceSuggestion(
                $this->getStudent($absence->getStudent()),
                $type->getBookLabel(),
                $type->getBookAttendanceType(),
                $type->isTypeWithZeroAbsenceLessons(),
                $type->getBookExcuseStatus(),
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                $absence->getType()->getBookAttendanceType() === LessonAttendanceType::Present ? $absence->getType()->getFlags()->map(fn(LessonAttendanceFlag $flag) => $flag->getId())->toArray() : [ ]
            );

            $suggestions[] = new PrioritizedSuggestion(
                $this->bookSettings->getSuggestionPriorityForAbsenceType($absence->getType()->getUuid()),
                $absence->getStudent(),
                $suggestion
            );
        }

        return $suggestions;
    }
}
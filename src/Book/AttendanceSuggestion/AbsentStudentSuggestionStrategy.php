<?php

namespace App\Book\AttendanceSuggestion;

use App\Book\StudentsResolver;
use App\Common\Entity\DateLesson;
use App\Book\Entity\AttendanceExcuseStatus;
use App\Book\Entity\AttendanceFlag;
use App\Book\Entity\AttendanceType;
use App\StudentAbsence\Entity\StudentAbsence;
use App\Common\Entity\Subject;
use App\Common\Entity\Tuition;
use App\StudentAbsence\Repository\StudentAbsenceRepositoryInterface;
use App\Response\Book\AttendanceSuggestion;
use App\Book\Settings\BookSettings;
use App\Framework\Utils\ArrayUtils;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbsentStudentSuggestionStrategy implements SuggestionStrategyInterface {

    use StudentTransformerTrait;

    public function __construct(private readonly StudentAbsenceRepositoryInterface $absenceRepository,
                                private readonly StudentsResolver $studentsResolver,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly BookSettings $bookSettings) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        $students = $this->studentsResolver->resolve($tuition);
        $suggestions = [ ];

        $absences = [ ];
        for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
            $absences = array_merge($absences, $this->absenceRepository->findByStudents($students, null, $date, $lessonNumber));
        }

        /** @var StudentAbsence[] $absences */
        $absences = ArrayUtils::unique($absences);

        foreach($absences as $absence) {
            $type = $absence->getType();
            $subjectIds = array_map(fn(Subject $subject) => $subject->getId(), $type->getSubjects()->toArray());

            if(count($subjectIds) > 0 && !in_array($tuition->getSubject()?->getId(), $subjectIds)) {
                // Ignore absence in case the type is bound to any subject and the subject of the given tuition does not match
                continue;
            }

            if($type->isMustApprove() && !$absence->isApproved() === true) {
                continue;
            }

            $lessons = [ ];
            for($lessonNumber = $lessonStart; $lessonNumber <= $lessonEnd; $lessonNumber++) {
                $dateLesson = (new DateLesson())->setDate($date)->setLesson($lessonNumber);

                if($dateLesson->isBetween($absence->getFrom(), $absence->getUntil())) {
                    $lessons[] = $lessonNumber;
                }
            }

            $suggestion = new AttendanceSuggestion(
                $this->getStudent($absence->getStudent()),
                $type->getBookLabel(),
                $lessons,
                $type->getBookAttendanceType()->value,
                $type->isTypeWithZeroAbsenceLessons(),
                $type->getBookExcuseStatus()->value,
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $absence->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                $absence->getType()->getBookAttendanceType() === AttendanceType::Present ? $absence->getType()->getFlags()->map(fn(AttendanceFlag $flag) => $flag->getId())->toArray() : [ ]
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
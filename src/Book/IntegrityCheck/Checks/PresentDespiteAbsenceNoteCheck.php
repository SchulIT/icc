<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Date\DateLessonExpander;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\Student;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This checks for attendances with present students despite an existing
 * absent note.
 */
class PresentDespiteAbsenceNoteCheck implements IntegrityCheckInterface {

    public const Name = 'present_despite_absence_note';
    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository,
                                private readonly StudentAbsenceRepositoryInterface $absenceRepository,
                                private readonly DateLessonExpander $dateLessonExpander,
                                private readonly TranslatorInterface $translator) { }

    public function check(Student $student, DateTime $start, DateTime $end): array {
        $violations = [ ];

        $attendances = $this->attendanceRepository->findByStudentAndDateRange($student, $start, $end);

        /** @var array<string, LessonAttendance> $presentAttendances */
        $presentAttendances = ArrayUtils::createArrayWithKeys(
            array_filter(
                $attendances,
                fn(LessonAttendance $attendance) => $attendance->getType() === LessonAttendanceType::Present
            ),
            function(LessonAttendance $attendance) {
                $keys = [];

                for($lesson = $attendance->getEntry()->getLessonStart(); $lesson <= $attendance->getEntry()->getLessonEnd(); $lesson++) {
                    $keys[] = sprintf('%s-%s', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $lesson);
                }

                return $keys;
            }
        );

        $absences = $this->absenceRepository->findByStudents([ $student ]);

        foreach($absences as $absence) {
            $lessons = $this->dateLessonExpander->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());

            foreach($lessons as $lesson) {
                $key = sprintf('%s-%s', $lesson->getDate()->format('Y-m-d'), $lesson->getLesson());

                if(array_key_exists($key, $presentAttendances)) {
                    $attendance = $presentAttendances[$key];
                    $violations[] = new IntegrityCheckViolation(clone $lesson->getDate(), $lesson->getLesson(), $attendance->getEntry()->getLesson(), $this->translator->trans('book.integrity_check.checks.present_despite_absence_note.violation'));
                }
            }
        }

        return $violations;
    }

    public function getName(): string {
        return self::Name;
    }
}
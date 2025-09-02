<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Date\DateLessonExpander;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
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

        $attendances = $this->attendanceRepository->findByStudentAndDateRange($student, $start, $end, true);

        /** @var array<string, Attendance> $presentAttendances */
        $presentAttendances = ArrayUtils::createArrayWithKeys(
            array_filter(
                $attendances,
                fn(Attendance $attendance) => $attendance->getType() === AttendanceType::Present
            ),
            fn(Attendance $attendance) => sprintf('%s-%s', $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d') : $attendance->getEvent()->getDate()->format('Y-m-d'), $attendance->getLesson())
        );

        $absences = $this->absenceRepository->findByStudents([ $student ]);
        $alreadyCheckedLessons = [ ];

        foreach($absences as $absence) {
            if($absence->getType()->isMustApprove() && $absence->isApproved() === false) {
                /**
                 * Ignore absences which were not approved
                 */
                continue;
            }

            if($absence->getType()->getBookAttendanceType() === AttendanceType::Present) {
                /**
                 * Ignore absences which are basically just information and no real absence (e.g. those with present attendance type)
                 */
                continue;
            }

            $lessons = $this->dateLessonExpander->expandRangeToDateLessons($absence->getFrom(), $absence->getUntil());

            foreach($lessons as $lesson) {
                $key = sprintf('%s-%s', $lesson->getDate()->format('Y-m-d'), $lesson->getLesson());

                if(!in_array($key, $alreadyCheckedLessons) && array_key_exists($key, $presentAttendances)) {
                    $attendance = $presentAttendances[$key];
                    $violations[] = new IntegrityCheckViolation(clone $lesson->getDate(), $lesson->getLesson(), $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson() : $attendance->getEvent(), $this->translator->trans('book.integrity_check.checks.present_despite_absence_note.violation'));
                }

                $alreadyCheckedLessons[] = $key;
            }
        }

        return $violations;
    }

    public function getName(): string {
        return self::Name;
    }
}
<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\Student;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\AttendanceStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use DateTime;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This check detects if more than one change in attendance is present per day.
 * Example 1: present, absent, present (a student was falsely absent)
 * Example 2: absent, present, absent (a student was falsely present)
 */
class MoreThanOneChangePerDayCheck implements IntegrityCheckInterface {

    public const Name = 'more_than_one_change';

    public function __construct(private readonly LessonAttendanceRepositoryInterface $lessonAttendanceRepository,
                                private readonly TranslatorInterface $translator,
                                private readonly Sorter $sorter,
                                private readonly TimetableSettings $timetableSettings) {}

    public function check(Student $student, DateTime $start, DateTime $end): array {
        $violations = [ ];

        if($end < $start) {
            throw new InvalidArgumentException('$start must be before $end');
        }

        $current = clone $start;
        while($current <= $end) {
            $attendancesForToday = $this->lessonAttendanceRepository->findByStudentAndDateRange($student, $current, $current);
            $this->sorter->sort($attendancesForToday, AttendanceStrategy::class);

            $changes = 0;
            $types = array_map(
                fn(Attendance $attendance) => $this->getCorrectedAttendanceType($attendance),
                $attendancesForToday
            );
            $presentCount = count(array_filter($types, fn(AttendanceType $attendance) => $attendance === AttendanceType::Present));
            $absentCount = count(array_filter($types, fn(AttendanceType $attendance) => $attendance === AttendanceType::Absent));
            $loserType = $presentCount < $absentCount ? AttendanceType::Present : AttendanceType::Absent;

            for($idx = 1; $idx < count($attendancesForToday); $idx++) {
                $lastAttendance = $attendancesForToday[$idx - 1];
                $currentAttendance = $attendancesForToday[$idx];

                $lastType = $this->getCorrectedAttendanceType($lastAttendance);
                $currentType = $this->getCorrectedAttendanceType($currentAttendance);

                if($lastType !== $currentType) {
                    $changes++;
                }
            }

            if($changes > 1) {
                /** @var Attendance[] $attendanceByLesson */
                $attendanceByLesson = ArrayUtils::createArrayWithKeys(
                    $attendancesForToday,
                    fn(Attendance $attendance) => $attendance->getLesson()
                );

                // As we do not know who's right or wrong, just blame them all :)
                for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                    $attendance = $attendanceByLesson[$lessonNumber] ?? null;

                    if($attendance !== null && $attendance->getType() === $loserType) {
                        $violations[] = new IntegrityCheckViolation(clone $current, $lessonNumber, $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson() : $attendance->getEvent(), $this->translator->trans('book.integrity_check.checks.more_than_one_change.violation'));
                    }
                }
            }

            $current = $current->modify('+1 day');
        }

        return $violations;
    }

    private function getCorrectedAttendanceType(Attendance $attendance): AttendanceType {
        $type = $attendance->getType();

        if($attendance->getType() === AttendanceType::Late) {
            $type = AttendanceType::Present;
        }

        if($attendance->getType() === AttendanceType::Absent && $attendance->isZeroAbsentLesson()) {
            $type = AttendanceType::Present;
        }

        return $type;
    }

    public function getName(): string {
        return self::Name;
    }
}
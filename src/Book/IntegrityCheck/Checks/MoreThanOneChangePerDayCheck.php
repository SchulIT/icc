<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\Student;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\LessonAttendenceStrategy;
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
            $this->sorter->sort($attendancesForToday, LessonAttendenceStrategy::class);

            $changes = 0;

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
                $attendanceByLesson = ArrayUtils::createArrayWithKeys(
                    $attendancesForToday,
                    function(LessonAttendance $attendance) {
                        $lessons = [ ];

                        for($lessonNumber = $attendance->getEntry()->getLesson()->getLessonStart(); $lessonNumber <= $attendance->getEntry()->getLesson()->getLessonEnd(); $lessonNumber++) {
                            $lessons[] = $lessonNumber;
                        }

                        return $lessons;
                    }
                );

                // As we do not know who's right or wrong, just blame them all :)
                for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                    $attendance = $attendanceByLesson[$lessonNumber] ?? null;
                    $violations[] = new IntegrityCheckViolation(clone $current, $lessonNumber, $attendance?->getEntry()->getLesson() , $this->translator->trans('book.integrity_check.checks.more_than_one_change.violation'));
                }
            }

            $current = $current->modify('+1 day');
        }

        return $violations;
    }

    private function getCorrectedAttendanceType(LessonAttendance $attendance): int {
        $type = $attendance->getType();

        if($attendance->getType() === LessonAttendanceType::Late) {
            $type = LessonAttendanceType::Present;
        }

        if($attendance->getType() === LessonAttendanceType::Absent && $attendance->getAbsentLessons() === 0) {
            $type = LessonAttendanceType::Present;
        }

        return $type;
    }

    public function getName(): string {
        return self::Name;
    }
}
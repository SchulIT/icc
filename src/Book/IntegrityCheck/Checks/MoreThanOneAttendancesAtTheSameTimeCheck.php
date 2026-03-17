<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\Student;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class MoreThanOneAttendancesAtTheSameTimeCheck implements IntegrityCheckInterface {

    public const string Name = 'more_than_one_attendances_at_the_same_time';

    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository, private readonly TranslatorInterface $translator) { }

    public function check(Student $student, DateTime $start, DateTime $end): array {
        $violations = [ ];

        $attendances = $this->attendanceRepository->findByStudentAndDateRange($student, $start, $end, true);
        $lessonKeys = [ ];
        $groupedAttendances = [ ];

        foreach($attendances as $attendance) {
            if($attendance->getEntry() !== null) {
                $date = $attendance->getEntry()->getLesson()->getDate();
            } else if($attendance->getEvent() !== null) {
                $date = $attendance->getEvent()->getDate();
            } else {
                continue; // ignore attendance
            }

            $lessonKey = sprintf('%s-%d', $date->format('Y-m-d'), $attendance->getLesson());

            if(!array_key_exists($lessonKey, $groupedAttendances)) {
                $groupedAttendances[$lessonKey] = [ ];
            }

            $groupedAttendances[$lessonKey][] = $attendance;
        }

        foreach($groupedAttendances as $lessonKey => $attendances) {
            $count = count($attendances);

            if($count <= 1) {
                continue; // only one attendance at this lesson -> PASS
            }

            $present = 0;
            $absent = 0;
            $absentZeroLessons = 0;

            /** @var Attendance $attendance */
            foreach($attendances as $attendance) {
                if($attendance->getType() === AttendanceType::Present || $attendance->getType() === AttendanceType::Late) {
                    $present++;
                } else if($attendance->isZeroAbsentLesson()) {
                    $absentZeroLessons++;
                } else {
                    $absent++;
                }
            }

            if(($present >= 1 && $absent >= 1) || ($present >= 1 && $present === $count) || ($absent >= 1 && $absent === $count)) {
                $violations[] = new IntegrityCheckViolation(clone $date, $attendance->getLesson(), $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson() : $attendance->getEvent(), $this->translator->trans('book.integrity_check.checks.more_than_one_attendances_at_the_same_time.violation'));
            }
        }

        return $violations;
    }

    public function getName(): string {
        return self::Name;
    }
}
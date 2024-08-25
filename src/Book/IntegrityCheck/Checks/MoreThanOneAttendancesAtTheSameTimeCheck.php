<?php

namespace App\Book\IntegrityCheck\Checks;

use App\Book\IntegrityCheck\IntegrityCheckInterface;
use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Entity\Student;
use App\Repository\LessonAttendanceRepositoryInterface;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class MoreThanOneAttendancesAtTheSameTimeCheck implements IntegrityCheckInterface {

    public const Name = 'more_than_one_attendances_at_the_same_time';

    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository, private readonly TranslatorInterface $translator) { }

    public function check(Student $student, DateTime $start, DateTime $end): array {
        $violations = [ ];

        $attendances = $this->attendanceRepository->findByStudentAndDateRange($student, $start, $end);
        $lessonKeys = [ ];

        foreach($attendances as $attendance) {
            if($attendance->getEntry() !== null) {
                $date = $attendance->getEntry()->getLesson()->getDate();
            } else if($attendance->getEvent() !== null) {
                $date = $attendance->getEvent()->getDate();
            } else {
                continue; // ignore attendance
            }

            $lessonKey = sprintf('%s-%d', $date->format('Y-m-d'), $attendance->getLesson());

            if(in_array($lessonKey, $lessonKeys)) {
                $violations[] = new IntegrityCheckViolation(clone $date, $attendance->getLesson(), $attendance->getEntry() !== null ? $attendance->getEntry()->getLesson() : $attendance->getEvent(), $this->translator->trans('book.integrity_check.checks.more_than_one_attendances_at_the_same_time.violation'));
            } else {
                $lessonKeys[] = $lessonKey;
            }
        }

        return $violations;
    }

    public function getName(): string {
        return self::Name;
    }
}
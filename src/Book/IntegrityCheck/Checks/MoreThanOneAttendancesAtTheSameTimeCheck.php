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
            for($lessonNumber = $attendance->getEntry()->getLessonStart(); $lessonNumber <= $attendance->getEntry()->getLessonEnd(); $lessonNumber++) {
                $lessonKey = sprintf('%s-%d', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $lessonNumber);

                if(in_array($lessonKey, $lessonKeys)) {
                    $violations[] = new IntegrityCheckViolation(clone $attendance->getEntry()->getLesson()->getDate(), $lessonNumber, $attendance->getEntry()->getLesson(), $this->translator->trans('book.integrity_check.checks.more_than_one_attendances_at_the_same_time.violation'));
                }
            }
        }

        return $violations;
    }

    public function getName(): string {
        return self::Name;
    }
}
<?php

namespace App\Book\Statistics;

use App\Book\Entity\Attendance;
use App\Book\Entity\AttendanceType;
use App\Common\Entity\Student;
use App\Book\Repository\LessonAttendanceRepositoryInterface;
use App\Timetable\Settings\TimetableSettings;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class StudentTimetableAttendanceStatisticsGenerator {

    private const string KEY_PATTERN = 'book.students.timetable_attendance_counter.%d.s-%s.e-%s';
    public const int LIFETIME_IN_SECONDS = 7200; // 2h

    public function __construct(
        private CacheInterface $cache,
        private LessonAttendanceRepositoryInterface $lessonAttendanceRepository,
        private TimetableSettings $timetableSettings,
        private DateHelper $dateHelper
    ) { }

    private function getCacheKey(Student $student, DateTime $start, DateTime $end): string {
        return sprintf(self::KEY_PATTERN, $student->getId(), $start->format('y-m-d'), $end->format('y-m-d'));
    }

    public function regenerate(Student $student, DateTime $start, DateTime $end): StudentTimetableAttendanceStatistics {
        $this->cache->delete($this->getCacheKey($student, $start, $end));
        return $this->getCount($student, $start, $end);
    }

    public function getCount(Student $student, DateTime $start, DateTime $end): StudentTimetableAttendanceStatistics {
        return $this->cache->get($this->getCacheKey($student, $start, $end), function(ItemInterface $item) use ($student, $start, $end): StudentTimetableAttendanceStatistics {
            $item->expiresAfter(self::LIFETIME_IN_SECONDS);

            $attendances = $this->lessonAttendanceRepository->findByStudent($student, $start, $end, true);
            $counter = [ ];

            foreach($this->timetableSettings->getDays() as $weekDay) {
                for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                    $relevantAttendances = array_filter(
                        $attendances,
                        fn(Attendance $attendance) => $attendance->getLesson() === $lessonNumber && (int)$attendance->getDate()->format('N') === $weekDay
                    );

                    $lateAttendances = array_filter(
                        $relevantAttendances,
                        fn(Attendance $attendance) => $attendance->getType() === AttendanceType::Late
                    );

                    $lateCount = array_sum(
                        array_map(
                            fn(Attendance $attendance) => $attendance->getLateMinutes(),
                            $lateAttendances
                        )
                    );

                    $absentAttendances = array_filter(
                        $relevantAttendances,
                        fn(Attendance $attendance) => $attendance->getType() === AttendanceType::Absent && $attendance->isZeroAbsentLesson() === false
                    );

                    $counter[] = new StudentTimetableAttendanceStatisticsCounter(
                        $weekDay,
                        $lessonNumber,
                        $lateCount,
                        count($lateAttendances),
                        count($absentAttendances),
                        count($relevantAttendances)
                    );
                }
            }

            return new StudentTimetableAttendanceStatistics(
                $student->getId(),
                $start,
                $end,
                $counter,
                $this->dateHelper->getNow()
            );
        });
    }
}
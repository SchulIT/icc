<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface TimetableLessonRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableLesson|null
     */
    public function findOneById(int $id): ?TimetableLesson;

    public function findOneByUuid(string $uuid): ?TimetableLesson;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Grade $grade
     * @return TimetableLesson[]
     */
    public function findAllByGrade(DateTime $start, DateTime $end, Grade $grade): array;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Teacher $teacher
     * @return TimetableLesson[]
     */
    public function findAllByTeacher(DateTime $start, DateTime $end, Teacher $teacher): array;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Room $room
     * @return TimetableLesson[]
     */
    public function findAllByRoom(DateTime $start, DateTime $end, Room $room): array;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Student $student
     * @return TimetableLesson[]
     */
    public function findAllByStudent(DateTime $start, DateTime $end, Student $student): array;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param array $subjects
     * @return TimetableLesson[]
     */
    public function findAllBySubjects(DateTime $start, DateTime $end, array $subjects): array;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Tuition[] $tuition
     * @return TimetableLesson[]
     */
    public function findAllByTuitions(DateTime $start, DateTime $end, array $tuition): array;

    /**
     * @param DateTime $date
     * @param Room $room
     * @param int $lessonNumber
     * @return TimetableLesson|null
     */
    public function findOneByPeriodAndRoomAndWeekAndDayAndLesson(DateTime $date, Room $room, int $lessonNumber): ?TimetableLesson;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return TimetableLesson[]
     */
    public function findAllByRange(DateTime $start, DateTime $end): array;

    /**
     * @return TimetableLesson[]
     */
    public function findAll(): array;

    /**
     * @param TimetableLesson $lesson
     */
    public function persist(TimetableLesson $lesson): void;

    /**
     * @param TimetableLesson $lesson
     */
    public function remove(TimetableLesson $lesson): void;

    /**
     * Remove all lessons between the given date range (both inclusive).
     * @param DateTime $start
     * @param DateTime $end
     */
    public function removeRange(DateTime $start, DateTime $end): void;

    /**
     * @param DateTime $dateTime The date from which lessons are removed (which is inclusive)
     * @return int Number of removed lessons
     */
    public function removeStartingFrom(DateTime $dateTime): int;

    public function getMissingByTeacherPaginator(int $itemsPerPage, int &$page, Teacher $teacher, DateTime $start, DateTime $end): Paginator;

    public function getMissingByGradePaginator(int $itemsPerPage, int &$page, Grade $grade, DateTime $start, DateTime $end): Paginator;

    public function getMissingByTuitionPaginator(int $itemsPerPage, int &$page, Tuition $tuition, DateTime $start, DateTime $end): Paginator;

    public function countMissingByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int;

    public function countMissingByGrade(Grade $grade, DateTime $start, DateTime $end): int;

    public function countMissingByTuition(Tuition $tuition, DateTime $start, DateTime $end): int;

    /**
     * @param Tuition[] $tuitions
     * @param Student|null $student
     * @return int
     */
    public function countHoldLessons(array $tuitions, ?Student $student): int;
}
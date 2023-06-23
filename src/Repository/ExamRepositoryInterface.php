<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface ExamRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Exam|null
     */
    public function findOneById(int $id): ?Exam;

    /**
     * @param string $externalId
     * @return Exam|null
     */
    public function findOneByExternalId(string $externalId): ?Exam;

    /**
     * @param int[] $ids
     * @return Exam[]
     */
    public function findAllByIds(array $ids): array;

    /**
     * @param Tuition[] $tuitions
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllByTuitions(array $tuitions, ?DateTime $today = null, bool $onlyPlanned = true);

    /**
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return array
     */
    public function findAllDatesByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null, bool $onlyPlanned = true);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllByTeacher(Teacher $teacher, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return array
     */
    public function findAllDatesByTeacher(Teacher $teacher, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param Student[] $students
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllByStudents(array $students, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param Student[] $students
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return array
     */
    public function findAllDatesByStudents(array $students, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllByGrade(Grade $grade, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return array
     */
    public function findAllDatesByGrade(Grade $grade, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param DateTime $doday
     * @return Exam[]
     */
    public function findAllByDate(DateTime $doday): array;

    /**
     * @param \DateTime $today
     * @param int $lesson
     * @return Exam[]
     */
    public function findAllByDateAndLesson(DateTime $today, int $lesson): array;

    /**
     * @param Room $room
     * @param DateTime $today
     * @return Exam[]
     */
    public function findAllByRoomAndDate(Room $room, DateTime $today): array;

    /**
     * @param Room $room
     * @param DateTime $today
     * @param int $lesson
     * @return Exam[]
     */
    public function findAllByRoomAndDateAndLesson(Room $room, DateTime $today, int $lesson): array;

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAll(?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true);

    /**
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Exam[]
     */
    public function findAllDates(?DateTime $today = null, bool $onlyPlanned = true);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllExternal(DateTime $today = null);

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return Exam[]
     */
    public function findAllExternalWithRange(DateTime $start, DateTime $end): array;

    /**
     * @param Exam $exam
     */
    public function persist(Exam $exam): void;

    /**
     * @param Exam $exam
     */
    public function remove(Exam $exam): void;

    public function removeBetween(DateTime $start, DateTime $end): int;

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param Grade|null $grade
     * @param Teacher|null $teacher
     * @param Student|null $student
     * @param StudyGroup|null $studyGroup
     * @param DateTime|null $today
     * @param bool $onlyPlanned If set to true, only planned exams are returned
     * @return Paginator
     */
    public function getPaginator(int $itemsPerPage, int &$page, ?Grade $grade = null, ?Teacher $teacher = null, ?Student $student = null, ?StudyGroup $studyGroup = null, bool $onlyPlanned = true, ?DateTime $today = null, ?DateTime $end = null, ?Section $section = null): Paginator;
}
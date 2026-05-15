<?php

namespace App\Substitution\Repository;

use App\Framework\Repository\TransactionalRepositoryInterface;
use App\Substitution\Entity\Absence;
use App\Common\Entity\Room;
use App\Common\Entity\Student;
use DateTime;

interface AbsenceRepositoryInterface extends TransactionalRepositoryInterface {
    public function findAll(): array;

    /**
     * Returns absent teachers for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllTeachers(DateTime $date): array;

    /**
     * Returns absent study groups for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllStudyGroups(DateTime $date): array;

    /**
     * @param DateTime $dateTime
     * @return Absence[]
     */
    public function findAllRooms(DateTime $dateTime): array;

    /**
     * @param Room $room
     * @param DateTime $dateTime
     * @return Absence[]
     */
    public function findAllByRoomAndDate(Room $room, DateTime $dateTime): array;

    /**
     * @param Student $student
     * @param DateTime $dateTime
     * @param int $lesson
     * @return Absence[]
     */
    public function findAllByStudentAndDateAndLesson(Student $student, DateTime $dateTime, int $lesson): array;

    /**
     * @param Student[] $students
     * @return Student[]
     */
    public function findAllStudentsByDateAndLesson(DateTime $dateTime, array $students, int $lesson): array;

    public function persist(Absence $person): void;

    public function removeAll(?DateTime $dateTime = null): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}
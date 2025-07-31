<?php

namespace App\Repository;

use App\Entity\BookEvent;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Teacher;
use DateTime;

interface BookEventRepositoryInterface {
    /**
     * @param Teacher $teacher
     * @param DateTime $start
     * @param DateTime $end
     * @return BookEvent[]
     */
    public function findByTeacher(Teacher $teacher, DateTime $start, Datetime $end): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return BookEvent[]
     */
    public function findByStudent(Student $student, DateTime $start, Datetime $end): array;

    /**
     * @param Grade $grade
     * @param Section $section
     * @param DateTime $start
     * @param DateTime $end
     * @return BookEvent[]
     */
    public function findByGrade(Grade $grade, Section $section, DateTime $start, DateTime $end): array;

    public function persist(BookEvent $bookEvent): void;

    public function remove(BookEvent $bookEvent): void;

    public function removeRange(DateTime $start, DateTime $end): int;
}
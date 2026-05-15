<?php

namespace App\Book\Repository;

use App\Book\Entity\BookComment;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use DateTime;

interface BookCommentRepositoryInterface {

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return BookComment[]
     */
    public function findAllByDateAndStudent(Student $student, DateTime $start, DateTime $end): array;

    public function countByDateAndStudent(Student $student, DateTime $start, DateTime $end): int;

    /**
     * @param Grade $grade
     * @param Section $section
     * @param DateTime $start
     * @param DateTime $end
     * @return BookComment[]
     */
    public function findAllByDateAndGrade(Grade $grade, Section $section, DateTime $start, DateTime $end): array;

    /**
     * @param Tuition $tuition
     * @param DateTime $start
     * @param DateTime $end
     * @return BookComment[]
     */
    public function findAllByDateAndTuition(Tuition $tuition, DateTime $start, DateTime $end): array;

    public function persist(BookComment $comment): void;

    public function remove(BookComment $comment): void;

    public function removeRange(DateTime $start, DateTime $end): int;

}
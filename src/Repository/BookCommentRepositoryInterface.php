<?php

namespace App\Repository;

use App\Entity\BookComment;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use DateTime;

interface BookCommentRepositoryInterface {

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return BookComment[]
     */
    public function findAllByDateAndStudent(Student $student, DateTime $start, DateTime $end): array;

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
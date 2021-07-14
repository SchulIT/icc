<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Tuition;
use DateTime;

interface LessonRepositoryInterface extends TransactionalRepositoryInterface {

    public function countByDate(DateTime $start, DateTime $end): int;

    /**
     * @param DateTime $dateTime
     * @return Lesson[]
     */
    public function findAllByDate(DateTime $dateTime): array;

    /**
     * @param Tuition[] $tuitions
     * @param DateTime $start
     * @param DateTime $end
     * @return Lesson[]
     */
    public function findAllByTuitions(array $tuitions, DateTime $start, DateTime $end): array;

    public function persist(Lesson $lesson): void;

    public function remove(Lesson $lesson): void;
}
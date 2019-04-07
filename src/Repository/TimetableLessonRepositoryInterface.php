<?php

namespace App\Repository;

use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;

interface TimetableLessonRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableLesson|null
     */
    public function findOneById(int $id): ?TimetableLesson;

    /**
     * @param TimetablePeriod $period
     * @return TimetableLesson[]
     */
    public function findAllByPeriod(TimetablePeriod $period);

    /**
     * @return TimetableLesson[]
     */
    public function findAll();

    /**
     * @param TimetableLesson $lesson
     */
    public function persist(TimetableLesson $lesson): void;

    /**
     * @param TimetableLesson $lesson
     */
    public function remove(TimetableLesson $lesson): void;
}
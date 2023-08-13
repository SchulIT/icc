<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollection;
use App\Entity\DateLesson;

class ExcuseNoteStatusItem {

    public function __construct(private readonly DateLesson $dateLesson, private readonly ?ExcuseCollection $excuseCollection) {

    }

    /**
     * @return DateLesson
     */
    public function getDateLesson(): DateLesson {
        return $this->dateLesson;
    }

    /**
     * @return ?ExcuseCollection
     */
    public function getCollection(): ?ExcuseCollection {
        return $this->excuseCollection;
    }
}
<?php

namespace App\Repository;

use App\Entity\StudyGroup;
use App\Entity\TimetableLessonAdditionalInformation;
use DateTime;

interface TimetableLessonAdditionalInformationRepositoryInterface {

    /**
     * @return TimetableLessonAdditionalInformation[]
     */
    public function findBy(DateTime $date, StudyGroup $studyGroup, int $lesson): array;

    public function persist(TimetableLessonAdditionalInformation $information): void;

    public function remove(TimetableLessonAdditionalInformation $information): void;
}
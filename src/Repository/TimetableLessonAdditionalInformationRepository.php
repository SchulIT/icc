<?php

namespace App\Repository;

use App\Entity\StudyGroup;
use App\Entity\TimetableLessonAdditionalInformation;
use DateTime;

class TimetableLessonAdditionalInformationRepository extends AbstractRepository implements TimetableLessonAdditionalInformationRepositoryInterface {

    public function findBy(DateTime $date, StudyGroup $studyGroup, int $lesson): array {
        $qb = $this->em->createQueryBuilder()
            ->select('i')
            ->from(TimetableLessonAdditionalInformation::class, 'i')
            ->where('i.date = :date')
            ->andWhere('i.studyGroup = :studyGroup')
            ->andWhere('i.lessonStart <= :lesson')
            ->andWhere('i.lessonEnd >= :lesson')
            ->setParameter('date', $date)
            ->setParameter('studyGroup', $studyGroup)
            ->setParameter('lesson', $lesson);

        return $qb->getQuery()->getResult();
    }

    public function persist(TimetableLessonAdditionalInformation $information): void {
        $this->em->persist($information);
        $this->em->flush();
    }

    public function remove(TimetableLessonAdditionalInformation $information): void {
        $this->em->remove($information);
        $this->em->flush();
    }
}
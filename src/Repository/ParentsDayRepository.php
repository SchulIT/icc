<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use DateTime;

class ParentsDayRepository extends AbstractRepository implements ParentsDayRepositoryInterface {

    public function findUpcoming(DateTime $from): array {
        return $this->em->createQueryBuilder()
            ->select('d')
            ->from(ParentsDay::class, 'd')
            ->where('d.date >= :date')
            ->setParameter('date', $from)
            ->getQuery()
            ->getResult();
    }

    public function findByDate(DateTime $date): array {
        return $this->em->getRepository(ParentsDay::class)
            ->findBy(['date' => $date], ['date' => 'desc']);
    }

    public function findAll(): array {
        return $this->em->getRepository(ParentsDay::class)
            ->findBy([], ['date' => 'desc']);
    }

    public function persist(ParentsDay $parentsDay): void {
        $this->em->persist($parentsDay);
        $this->em->flush();
    }

    public function remove(ParentsDay $parentsDay): void {
        $this->em->remove($parentsDay);
        $this->em->flush();
    }
}
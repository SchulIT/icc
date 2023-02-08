<?php

namespace App\Repository;

use App\Entity\Section;
use DateTime;

class SectionRepository extends AbstractRepository implements SectionRepositoryInterface {

    public function findOneByDate(DateTime $dateTime): ?Section {
        return $this->em
            ->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->where('s.start <= :date')
            ->andWhere('s.end >= :date')
            ->setParameter('date', $dateTime)
            ->setMaxResults(1)
            ->setCacheable(true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByNumberAndYear(int $number, int $year): ?Section {
        return $this->em
            ->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->where('s.number = :number')
            ->andWhere('s.year = :year')
            ->setParameter('number', $number)
            ->setParameter('year', $year)
            ->setCacheable(true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneById(int $id): ?Section {
        return $this->em->getRepository(Section::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    public function persist(Section $section): void {
        $this->em->persist($section);
        $this->em->flush();
    }

    public function remove(Section $section): void {
        $this->em->remove($section);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Section::class, 's')
            ->orderBy('s.year')
            ->addOrderBy('s.number')
            ->getQuery()
            ->getResult();
    }

    public function findOneByUuid(string $uuid): ?Section {
        return $this->em->getRepository(Section::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }
}
<?php

namespace App\Repository;

use App\Entity\Room;
use App\Rooms\RoomQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RoomRepository extends AbstractTransactionalRepository implements RoomRepositoryInterface {



    private function getDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['r', 'rt', 'rti'])
            ->from(Room::class, 'r')
            ->leftJoin('r.tags', 'rti')
            ->leftJoin('rti.tag', 'rt');
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Room {
        return $this->getDefaultQueryBuilder()
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Room {
        return $this->getDefaultQueryBuilder()
            ->where('r.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?Room {
        return $this->getDefaultQueryBuilder()
            ->where('r.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->getDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllExternal(): array {
        return $this->getDefaultQueryBuilder()
            ->andWhere('r.externalId IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function persist(Room $room): void {
        $this->em->persist($room);
        $this->flushIfNotInTransaction();

    }

    public function remove(Room $room): void {
        $this->em->remove($room);
        $this->flushIfNotInTransaction();
    }





}
<?php

namespace App\Repository;

use App\Entity\Room;
use App\Rooms\RoomQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RoomRepository extends AbstractTransactionalRepository implements RoomRepositoryInterface {

    private $roomTagRepository;

    public function __construct(EntityManagerInterface $em, RoomTagRepositoryInterface $roomTagRepository) {
        parent::__construct($em);

        $this->roomTagRepository = $roomTagRepository;
    }

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

    public function persist(Room $room): void {
        $this->em->persist($room);
        $this->flushIfNotInTransaction();

    }

    public function remove(Room $room): void {
        $this->em->remove($room);
        $this->flushIfNotInTransaction();
    }


    /**
     * @inheritDoc
     */
    public function findAllByQuery(RoomQuery $query): array {
        $qb = $this->getDefaultQueryBuilder();


        /**
         * Step 1: select room IDs
         */
        $qbInner = $this->em->createQueryBuilder();
        $qbInner->select('rInner.id')
            ->from(Room::class, 'rInner')
            ->leftJoin('rInner.tags', 'tiInner')
            ->leftJoin('tiInner.tag', 'tInner')
            ->groupBy('rInner.name');

        if($query->hasSeats()) {
            $qbInner->andWhere('rInner.seats >= :seats');
            $qb->setParameter('seats', $query->getSeatsValueOrDefault());
        }

        if($query->hasName()) {
            $qbInner->andWhere(
                $qbInner->expr()->orX(
                    'rInner.name LIKE :q',
                    'rInner.description LIKE :q'
                )
            );
            $qb->setParameter('q', '%' . $query->getName() . '%');
        }

        $tagIds = [ ];

        foreach($this->roomTagRepository->findAll() as $tag) {
            if($query->hasTag($tag)) {
                if($tag->hasValue()) {
                    $tagIds[] = $tag->getId();
                    $qbInner
                        ->andWhere(
                            $qbInner->expr()->orX(
                                'tInner.id != :tag' . $tag->getId() . 'id',
                                'tiInner.value >= :tag' . $tag->getId() . 'value'
                            )
                        );

                    $qb->setParameter('tag' . $tag->getId() . 'id', $tag->getId())
                        ->setParameter('tag' . $tag->getId() . 'value', $query->getValueOrDefault($tag));
                } else if(!$tag->hasValue()) {
                    $tagIds[] = $tag->getId();
                }
            }
        }

        if(count($tagIds) > 0) {
            $qbInner
                ->andWhere(
                    $qbInner->expr()->in('tInner.id', ':tags')
                )
                ->having('COUNT(tInner.id) = :tagCount');

            $qb->setParameter('tags', $tagIds)
                ->setParameter('tagCount', count($tagIds));
        }

        $qb->where(
            $qb->expr()->in('r.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }
}
<?php

namespace App\Repository;

use App\Entity\ResourceEntity;
use App\Entity\Room;
use App\Rooms\RoomQuery;
use Doctrine\ORM\EntityManagerInterface;

class ResourceRepository extends AbstractRepository implements ResourceRepositoryInterface {

    private RoomTagRepositoryInterface $roomTagRepository;

    public function __construct(EntityManagerInterface $em, RoomTagRepositoryInterface $roomTagRepository) {
        parent::__construct($em);

        $this->roomTagRepository = $roomTagRepository;
    }

    public function findOneByName(string $name): ?ResourceEntity {
        return $this->em->getRepository(ResourceEntity::class)
            ->findOneBy([
                'name' => $name
            ]);
    }

    public function findOneByUuid(string $uuid): ?ResourceEntity {
        return $this->em->getRepository(ResourceEntity::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllNonRooms(): array {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ResourceEntity::class, 'r')
            ->where('r NOT INSTANCE OF :roomClass')
            ->setParameter('roomClass', $this->em->getClassMetadata(Room::class));

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByQuery(RoomQuery $query): array {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ResourceEntity::class, 'r');


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
            $qbInner->andWhere('rInner.capacity >= :capacity');
            $qb->setParameter('capacity', $query->getSeatsValueOrDefault());
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
                } else {
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
            )
            ->orWhere(
                'r NOT INSTANCE OF :roomClass'
            )
            ->setParameter('roomClass', $this->em->getClassMetadata(Room::class));

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(ResourceEntity::class)
            ->findAll();
    }

    public function persist(ResourceEntity $resource): void {
        $this->em->persist($resource);
        $this->em->flush();
    }

    public function remove(ResourceEntity $resource): void {
        $this->em->remove($resource);
        $this->em->flush();
    }
}
<?php

namespace App\Repository;

use App\Entity\ReturnItem;
use App\Entity\ReturnItemType;
use Override;

class ReturnItemTypeRepository extends AbstractRepository implements ReturnItemTypeRepositoryInterface {

    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(ReturnItemType::class)->findBy([], ['displayName' => 'ASC']);
    }

    #[Override]
    public function countReturnsForType(ReturnItemType $type): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(ReturnItem::class, 'i')
            ->leftJoin('i.type', 't')
            ->where('i.id = :type')
            ->setParameter('type', $type->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    #[Override]
    public function persist(ReturnItemType $returnItemType): void {
        $this->em->persist($returnItemType);
        $this->em->flush();
    }

    #[Override]
    public function remove(ReturnItemType $returnItemType): void {
        $this->em->remove($returnItemType);
        $this->em->flush();
    }
}
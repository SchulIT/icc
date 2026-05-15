<?php

namespace App\Chat\Repository;

use App\Chat\Entity\ChatTag;
use App\Common\Entity\UserType;
use App\Framework\Repository\AbstractRepository;
use App\Chat\Repository\ChatTagRepositoryInterface;

class ChatTagRepository extends AbstractRepository implements ChatTagRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(ChatTag::class)
            ->findBy([], ['name' => 'ASC']);
    }

    public function findForUserType(UserType $userType): array {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(ChatTag::class, 't')
            ->leftJoin('t.userTypes', 'u')
            ->where('u.userType = :userType')
            ->setParameter('userType', $userType)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function persist(ChatTag $chatTag): void {
        $this->em->persist($chatTag);
        $this->em->flush();
    }

    public function remove(ChatTag $chatTag): void {
        $this->em->remove($chatTag);
        $this->em->flush();
    }
}
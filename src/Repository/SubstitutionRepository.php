<?php

namespace App\Repository;

use App\Entity\Substitution;
use Doctrine\ORM\EntityManagerInterface;

class SubstitutionRepository implements SubstitutionRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Substitution|null
     */
    public function findOneById(int $id): ?Substitution {
        return $this->em->getRepository(Substitution::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Substitution|null
     */
    public function findOneByExternalId(string $externalId): ?Substitution {
        return $this->em->getRepository(Substitution::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @param \DateTime $date
     * @return Substitution[]
     */
    public function findAllByDate(\DateTime $date) {
        return $this->em->getRepository(Substitution::class)
            ->findBy([
                'date' => $date
            ]);
    }

    /**
     * @param Substitution $substitution
     */
    public function persist(Substitution $substitution): void {
        $this->em->persist($substitution);
        $this->em->flush();
    }

    /**
     * @param Substitution $substitution
     */
    public function remove(Substitution $substitution): void {
        $this->em->remove($substitution);
        $this->em->flush();
    }
}
<?php

namespace App\Repository;

use App\Entity\TimetableWeek;

class TimetableWeekRepository extends AbstractRepository implements TimetableWeekRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetableWeek {
        return $this->em->getRepository(TimetableWeek::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByKey(string $key): ?TimetableWeek {
        return $this->em->getRepository(TimetableWeek::class)
            ->findOneBy([
                'key' => $key
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(TimetableWeek::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetableWeek $week): void {
        $this->em->persist($week);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetableWeek $week): void {
        $this->em->remove($week);
        $this->em->flush();
    }
}
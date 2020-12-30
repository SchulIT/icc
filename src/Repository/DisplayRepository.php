<?php

namespace App\Repository;

use App\Entity\Display;

class DisplayRepository extends AbstractRepository implements DisplayRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(Display::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(Display $display): void {
        $this->em->persist($display);
        $this->em->flush();
    }

    public function remove(Display $display) {
        $this->em->remove($display);
        $this->em->flush();
    }
}
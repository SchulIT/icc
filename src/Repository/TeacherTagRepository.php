<?php

namespace App\Repository;

use App\Entity\TeacherTag;

class TeacherTagRepository extends AbstractRepository implements TeacherTagRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(TeacherTag::class)->findBy([], [
            'name' => 'asc'
        ]);
    }

    public function persist(TeacherTag $tag): void {
        $this->em->persist($tag);
        $this->em->flush();
    }

    public function remove(TeacherTag $tag): void {
        $this->em->remove($tag);
        $this->em->flush();
    }
}
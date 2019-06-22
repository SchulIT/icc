<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface {

    /**
     * @param int $id
     * @return Document|null
     */
    public function findOneById(int $id): ?Document {
        return $this->em->getRepository(Document::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param DocumentCategory $category
     * @return Document[]
     */
    public function findAllByCategory(DocumentCategory $category) {
        return $this->em->getRepository(Document::class)
            ->findBy([
                'category' => $category
            ]);
    }

    /**
     * @return Document[]
     */
    public function findAll() {
        return $this->em->getRepository(Document::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @param Document $document
     */
    public function persist(Document $document): void {
        $this->em->persist($document);
        $this->em->flush();
    }

    /**
     * @param Document $document
     */
    public function remove(Document $document): void {
        $this->em->persist($document);
        $this->em->flush();
    }
}
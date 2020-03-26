<?php

namespace App\Repository;

use App\Entity\WikiArticle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator;

class WikiArticleRepository extends AbstractRepository implements WikiArticleRepositoryInterface {

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);

        $em->getConfiguration()->addCustomHydrationMode('tree', TreeObjectHydrator::class);
    }

    public function findOneById(int $id): ?WikiArticle {
        return $this->em
            ->getRepository(WikiArticle::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(WikiArticle::class)
            ->createQueryBuilder('node')
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree');
    }

    /**
     * @inheritDoc
     */
    public function findAllByQuery(string $q): array {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['a', 'c', 'p'])
            ->from(WikiArticle::class, 'a')
            ->leftJoin('a.children', 'c')
            ->leftJoin('a.parent', 'p')
            ->where(
                $qb->expr()->orX(
                    'MATCH(a.title) AGAINST (:query) > 0',
                    'MATCH(a.content) AGAINST (:query) > 0',
                    $qb->expr()->like('a.title', ':queryLike')
                )
            )
            ->setParameter('query', $q)
            ->setParameter('queryLike', '%' . $q . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function persist(WikiArticle $article): void {
        $this->em->persist($article);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(WikiArticle $article): void {
        $this->em->remove($article);
        $this->em->flush();
    }

}
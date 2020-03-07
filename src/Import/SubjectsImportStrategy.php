<?php

namespace App\Import;

use App\Entity\Subject;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\SubjectData;
use App\Utils\ArrayUtils;

class SubjectsImportStrategy implements ImportStrategyInterface {

    private $repository;

    public function __construct(SubjectRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @return array<string, Subject>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function(Subject $subject) {
                return $subject->getExternalId();
            }
        );
    }

    /**
     * @param SubjectData $data
     * @return Subject
     */
    public function createNewEntity($data) {
        $subject = (new Subject())
            ->setExternalId($data->getId());
        $this->updateEntity($subject, $data);

        return $subject;
    }

    /**
     * @param SubjectData $object
     * @param array<string, Subject> $existingEntities
     * @return Subject|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Subject $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Subject $entity
     * @param SubjectData $data
     */
    public function updateEntity($entity, $data): void {
        $entity->setName($data->getName());
        $entity->setAbbreviation($data->getAbbreviation());
    }

    /**
     * @param Subject $entity
     */
    public function persist($entity): void {
        $this->repository->persist($entity);
    }

    /**
     * @param Subject $entity
     */
    public function remove($entity): void {
        $this->repository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }
}
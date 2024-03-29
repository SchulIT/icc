<?php

namespace App\Import;

use App\Entity\Subject;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\SubjectData;
use App\Request\Data\SubjectsData;
use App\Utils\ArrayUtils;

class SubjectsImportStrategy implements ImportStrategyInterface {

    public function __construct(private SubjectRepositoryInterface $repository)
    {
    }

    /**
     * @param SubjectsData $requestData
     * @return array<string, Subject>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(true),
            fn(Subject $subject) => $subject->getExternalId()
        );
    }

    /**
     * @param SubjectData $data
     * @param SubjectsData $requestData
     * @return Subject
     */
    public function createNewEntity($data,$requestData) {
        $subject = (new Subject())
            ->setExternalId($data->getId());
        $this->updateEntity($subject, $data, $requestData);

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
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Subject $entity
     * @param SubjectData $data
     * @param SubjectsData $requestData
     */
    public function updateEntity($entity, $data, $requestData): void {
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
    public function remove($entity, $requestData): bool {
        $this->repository->remove($entity);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    /**
     * @param SubjectsData $data
     * @return SubjectData[]
     */
    public function getData($data): array {
        return $data->getSubjects();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Subject::class;
    }
}
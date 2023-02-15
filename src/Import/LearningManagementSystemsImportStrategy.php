<?php

namespace App\Import;

use App\Entity\LearningManagementSystem;
use App\Repository\LearningManagementSystemRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\LearningManagementSystemData;
use App\Request\Data\LearningManagementSystemsData;
use App\Utils\ArrayUtils;

class LearningManagementSystemsImportStrategy implements ImportStrategyInterface {

    public function __construct(private readonly LearningManagementSystemRepositoryInterface $repository) {

    }

    public function getEntityClassName(): string {
        return LearningManagementSystem::class;
    }

    /**
     * @param LearningManagementSystemsData $requestData
     * @return LearningManagementSystemData[]
     */
    public function getData($requestData): array {
        return $requestData->getLms();
    }

    /**
     * @param LearningManagementSystemsData $requestData
     * @return LearningManagementSystem[]
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(LearningManagementSystem $lms) => $lms->getExternalId()
        );
    }

    /**
     * @param LearningManagementSystemData $data
     * @param LearningManagementSystemsData $requestData
     * @return LearningManagementSystem
     */
    public function createNewEntity($data, $requestData): LearningManagementSystem {
        $lms = (new LearningManagementSystem())
            ->setExternalId($data->getId());
        $this->updateEntity($lms, $data, $requestData);

        return $lms;
    }

    /**
     * @param LearningManagementSystemData $object
     * @param array<string, LearningManagementSystem> $existingEntities
     * @return LearningManagementSystem|null
     */
    public function getExistingEntity($object, array $existingEntities): ?LearningManagementSystem {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param LearningManagementSystem $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param LearningManagementSystem $entity
     * @param LearningManagementSystemData $data
     * @param LearningManagementSystemsData $requestData
     * @return void
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
    }

    /**
     * @param LearningManagementSystem $entity
     * @return void
     */
    public function persist($entity): void {
        $this->repository->persist($entity);
    }

    /**
     * @param LearningManagementSystem $entity
     * @param LearningManagementSystemsData $requestData
     * @return bool
     */
    public function remove($entity, $requestData): bool {
        $this->repository->remove($entity);
        return true;
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }
}
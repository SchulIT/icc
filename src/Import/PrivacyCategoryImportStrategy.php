<?php

namespace App\Import;

use App\Entity\PrivacyCategory;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\PrivacyCategoriesData;
use App\Request\Data\PrivacyCategoryData;
use App\Utils\ArrayUtils;

class PrivacyCategoryImportStrategy implements ImportStrategyInterface {

    public function __construct(private PrivacyCategoryRepositoryInterface $repository)
    {
    }

    /**
     * @param PrivacyCategoriesData $data
     * @return PrivacyCategoryData[]
     */
    public function getData($data): array {
        return $data->getCategories();
    }

    /**
     * @param PrivacyCategoriesData $requestData
     * @return PrivacyCategory[]
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(PrivacyCategory $category) => $category->getExternalId()
        );
    }

    /**
     * @param PrivacyCategoryData $data
     * @param PrivacyCategoriesData $requestData
     * @return PrivacyCategory
     */
    public function createNewEntity($data, $requestData) {
        $category = (new PrivacyCategory())
            ->setExternalId($data->getId());
        $this->updateEntity($category, $data, $requestData);

        return $category;
    }

    /**
     * @param PrivacyCategoryData $object
     * @param PrivacyCategory[] $existingEntities
     * @return PrivacyCategory|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param PrivacyCategory $entity
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param PrivacyCategory $entity
     * @param PrivacyCategoriesData $requestData
     * @param PrivacyCategoryData $data
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setLabel($data->getLabel());
        $entity->setDescription($data->getDescription());
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->repository->persist($entity);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return PrivacyCategory::class;
    }
}
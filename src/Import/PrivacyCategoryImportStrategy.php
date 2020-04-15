<?php

namespace App\Import;

use App\Entity\PrivacyCategory;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\PrivacyCategoriesData;
use App\Request\Data\PrivacyCategoryData;
use App\Utils\ArrayUtils;

class PrivacyCategoryImportStrategy implements ImportStrategyInterface {

    private $repository;

    public function __construct(PrivacyCategoryRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @param PrivacyCategoriesData $data
     * @return PrivacyCategoryData[]
     */
    public function getData($data): array {
        return $data->getCategories();
    }

    /**
     * @inheritDoc
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function (PrivacyCategory $category) {
                return $category->getExternalId();
            }
        );
    }

    /**
     * @param PrivacyCategoryData $data
     * @return PrivacyCategory
     */
    public function createNewEntity($data) {
        $category = (new PrivacyCategory())
            ->setExternalId($data->getId());
        $this->updateEntity($category, $data);

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
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param PrivacyCategory $entity
     * @param PrivacyCategoryData $data
     */
    public function updateEntity($entity, $data): void {
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
<?php

namespace App\Import;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AppointmentCategoriesData;
use App\Request\Data\AppointmentCategoryData;
use App\Utils\ArrayUtils;

class AppointmentCategoriesImportStrategy implements ImportStrategyInterface {

    public function __construct(private AppointmentCategoryRepositoryInterface $repository)
    {
    }

    /**
     * @param AppointmentCategoriesData $requestData
     * @return array<string, AppointmentCategory>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(AppointmentCategory $category) => $category->getExternalId()
        );
    }

    /**
     * @param AppointmentCategoryData $data
     * @param AppointmentCategoriesData $requestData
     * @return AppointmentCategory
     */
    public function createNewEntity($data, $requestData) {
        $category = (new AppointmentCategory())
            ->setExternalId($data->getId());

        $this->updateEntity($category, $data, $requestData);
        return $category;
    }

    /**
     * @param AppointmentCategoryData $object
     * @param array<string, AppointmentCategory> $existingEntities
     * @return AppointmentCategory|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param AppointmentCategory $entity
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param AppointmentCategory $entity
     * @param AppointmentCategoryData $data
     * @param AppointmentCategoriesData $requestData
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
        $entity->setColor($data->getColor());
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
     * @param AppointmentCategoriesData $data
     * @return AppointmentCategoryData[]
     */
    public function getData($data): array {
        return $data->getCategories();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return AppointmentCategory::class;
    }
}
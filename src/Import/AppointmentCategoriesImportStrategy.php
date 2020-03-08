<?php

namespace App\Import;

use App\Entity\AppointmentCategory;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AppointmentCategoriesData;
use App\Request\Data\AppointmentCategoryData;
use App\Utils\ArrayUtils;

class AppointmentCategoriesImportStrategy implements ImportStrategyInterface {

    private $repository;

    public function __construct(AppointmentCategoryRepositoryInterface $appointmentCategoryRepository) {
        $this->repository = $appointmentCategoryRepository;
    }

    /**
     * @return array<string, AppointmentCategory>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function(AppointmentCategory $category) {
                return $category->getExternalId();
            }
        );
    }

    /**
     * @param AppointmentCategoryData $data
     * @return AppointmentCategory
     */
    public function createNewEntity($data) {
        $category = (new AppointmentCategory())
            ->setExternalId($data->getId());

        $this->updateEntity($category, $data);
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
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param AppointmentCategory $entity
     * @param AppointmentCategoryData $data
     */
    public function updateEntity($entity, $data): void {
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
    public function remove($entity): void {
        $this->repository->remove($entity);
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
}
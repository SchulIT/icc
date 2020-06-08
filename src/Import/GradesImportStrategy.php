<?php

namespace App\Import;

use App\Entity\Grade;
use App\Repository\GradeRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\GradeData;
use App\Request\Data\GradesData;
use App\Utils\ArrayUtils;

class GradesImportStrategy implements ImportStrategyInterface {

    private $repository;

    public function __construct(GradeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @param GradesData $requestData
     * @return array<string, Grade>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function (Grade $grade) {
                return $grade->getExternalId();
            }
        );
    }

    /**
     * @param GradeData $data
     * @param GradesData $requestData
     * @return Grade
     */
    public function createNewEntity($data, $requestData) {
        $grade = (new Grade())
            ->setExternalId($data->getId());

        $this->updateEntity($grade, $data, $requestData);
        return $grade;
    }

    /**
     * @param GradeData $object
     * @param array<string, Grade> $existingEntities
     * @return Grade|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Grade $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Grade $entity
     * @param GradesData $requestData
     * @param GradeData $data
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
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
     * @param GradesData $data
     * @return GradeData[]
     */
    public function getData($data): array {
        return $data->getGrades();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Grade::class;
    }
}
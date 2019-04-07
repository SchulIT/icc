<?php

namespace App\Import;

use App\Entity\TimetablePeriod;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TimetablePeriodData;
use App\Utils\ArrayUtils;

class TimetablePeriodsImportStrategy implements ImportStrategyInterface {

    private $repository;

    public function __construct(TimetablePeriodRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @return array<string, TimetablePeriod>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function(TimetablePeriod $period) {
                return $period->getExternalId();
            }
        );
    }

    /**
     * @param TimetablePeriodData $data
     * @return TimetablePeriod
     */
    public function createNewEntity($data) {
        $period = (new TimetablePeriod())
            ->setExternalId($data->getId());
        $this->updateEntity($period, $data);

        return $period;
    }

    /**
     * @param TimetablePeriodData $object
     * @param array<string, TimetablePeriod> $existingEntities
     * @return TimetablePeriod|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param TimetablePeriod $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param TimetablePeriod $entity
     * @param TimetablePeriodData $data
     */
    public function updateEntity($entity, $data): void {
        $entity->setName($data->getName());
        $entity->setStart($data->getStart());
        $entity->setEnd($data->getEnd());

        // Todo: Validate that periods do not overlap
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
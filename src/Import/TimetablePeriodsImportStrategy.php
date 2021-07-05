<?php

namespace App\Import;

use App\Entity\TimetablePeriod;
use App\Repository\SectionRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TimetablePeriodData;
use App\Request\Data\TimetablePeriodsData;
use App\Utils\ArrayUtils;

class TimetablePeriodsImportStrategy implements ImportStrategyInterface, NonRemovalImportStrategyInterface {

    private $repository;
    private $sectionRepository;

    public function __construct(TimetablePeriodRepositoryInterface $repository, SectionRepositoryInterface $sectionRepository) {
        $this->repository = $repository;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @param TimetablePeriodsData $requestData
     * @return array<string, TimetablePeriod>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            function(TimetablePeriod $period) {
                return $period->getExternalId();
            }
        );
    }

    /**
     * @param TimetablePeriodData $data
     * @param TimetablePeriodsData $requestData
     * @return TimetablePeriod
     */
    public function createNewEntity($data, $requestData) {
        $period = (new TimetablePeriod())
            ->setExternalId($data->getId());
        $this->updateEntity($period, $data, $requestData);

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
     * @param TimetablePeriodsData $requestData
     * @throws SectionNotResolvableException
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
        $entity->setStart($data->getStart());
        $entity->setEnd($data->getEnd());

        $startSection = $this->sectionRepository->findOneByDate($entity->getStart());
        if($startSection === null) {
            throw new SectionNotResolvableException($entity->getStart());
        }

        $endSection = $this->sectionRepository->findOneByDate($entity->getEnd());
        if($endSection === null) {
            throw new SectionNotResolvableException($entity->getEnd());
        }

        if($startSection !== $endSection) {
            throw new ImportException('Timetable period is part of two sections which is not allowed.');
        }

        $entity->setSection($startSection);

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
     * @param TimetablePeriodsData $data
     * @return TimetablePeriodData[]
     */
    public function getData($data): array {
        return $data->getPeriods();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return TimetablePeriod::class;
    }

    /**
     * @inheritDoc
     */
    public function preventRemoval($data): bool {
        return true;
    }
}
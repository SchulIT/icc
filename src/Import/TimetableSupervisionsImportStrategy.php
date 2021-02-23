<?php

namespace App\Import;

use App\Entity\TimetableSupervision;
use App\Entity\Week;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\WeekRepositoryInterface;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class TimetableSupervisionsImportStrategy implements ImportStrategyInterface {

    private $supervisionRepository;
    private $periodRepository;
    private $weekRepository;
    private $teacherRepository;

    private $isInitialized = false;
    private $weeksCache = [ ];

    public function __construct(TimetableSupervisionRepositoryInterface $supervisionRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                WeekRepositoryInterface $weekRepository, TeacherRepositoryInterface $teacherRepository) {
        $this->supervisionRepository = $supervisionRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->teacherRepository = $teacherRepository;
    }

    private function initialize() {
        if($this->isInitialized === true) {
            return;
        }

        $this->weeksCache = ArrayUtils::createArrayWithKeys(
            $this->weekRepository->findAll(),
            function(Week $week) {
                return $week->getNumber();
            }
        );

        $this->isInitialized = true;
    }

    /**
     * @param TimetableSupervisionsData $requestData
     * @return array<string, TimetableSupervision>
     * @throws ImportException
     */
    public function getExistingEntities($requestData): array {
        $period = $this->periodRepository->findOneByExternalId($requestData->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Cannot find period with external ID "%s".', $requestData->getPeriod()));
        }

        return ArrayUtils::createArrayWithKeys(
            $this->supervisionRepository->findAllByPeriod($period),
            function(TimetableSupervision $supervision) {
                return $supervision->getExternalId();
            }
        );
    }

    /**
     * @param TimetableSupervisionData $data
     * @return TimetableSupervision
     * @param TimetableSupervisionsData $requestData
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $supervision = (new TimetableSupervision())
            ->setExternalId($data->getId());
        $this->updateEntity($supervision, $data, $requestData);

        return $supervision;
    }

    /**
     * @param TimetableSupervisionData $object
     * @param array<string, TimetableSupervision> $existingEntities
     * @return TimetableSupervision|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param TimetableSupervision $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param TimetableSupervision $entity
     * @param TimetableSupervisionData $data
     * @param TimetableSupervisionsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $this->initialize();

        $period = $this->periodRepository->findOneByExternalId($requestData->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Period "%s" on timetable supervision ID "%s" was not found.', $requestData->getPeriod(), $data->getId()));
        }

        $week = $this->weekRepository->findOneByKey($data->getWeek());

        if($week === null) {
            throw new ImportException(sprintf('Week "%s" on timetable supervision ID "%s" was not found.', $data->getWeek(), $data->getId()));
        }

        $teacher = $this->teacherRepository->findOneByExternalId($data->getTeacher());

        if($teacher === null) {
            throw new ImportException(sprintf('Teacher with ID "%s" on timetable supervision ID "%s" was not found.', $data->getTeacher(), $data->getId()));
        }

        $weeks = ArrayUtils::findAllWithKeys($this->weeksCache, $data->getWeeks());
        CollectionUtils::synchronize(
            $entity->getWeeks(),
            $weeks,
            function(Week $week) {
                return $week->getNumber();
            }
        );

        $entity->setPeriod($period);
        $entity->setTeacher($teacher);
        $entity->setDay($data->getDay());
        $entity->setLesson($data->getLesson());
        $entity->setIsBefore($data->isBefore());
        $entity->setLocation($data->getLocation());
    }

    /**
     * @param TimetableSupervision $entity
     */
    public function persist($entity): void {
        $this->supervisionRepository->persist($entity);
    }

    /**
     * @param TimetableSupervision $entity
     */
    public function remove($entity): void {
        $this->supervisionRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->supervisionRepository;
    }

    /**
     * @param TimetableSupervisionsData $data
     * @return TimetableSupervisionData[]
     */
    public function getData($data): array {
        return $data->getSupervisions();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return TimetableSupervision::class;
    }
}
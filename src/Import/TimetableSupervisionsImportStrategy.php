<?php

namespace App\Import;

use App\Entity\TimetableSupervision;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use App\Utils\ArrayUtils;

class TimetableSupervisionsImportStrategy implements ImportStrategyInterface {

    private $supervisionRepository;
    private $periodRepository;
    private $weekRepository;
    private $teacherRepository;

    public function __construct(TimetableSupervisionRepositoryInterface $supervisionRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableWeekRepositoryInterface $weekRepository, TeacherRepositoryInterface $teacherRepository) {
        $this->supervisionRepository = $supervisionRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @return array<string, TimetableSupervision>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->supervisionRepository->findAll(),
            function(TimetableSupervision $supervision) {
                return $supervision->getExternalId();
            }
        );
    }

    /**
     * @param TimetableSupervisionData $data
     * @return TimetableSupervision
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $supervision = (new TimetableSupervision())
            ->setExternalId($data->getId());
        $this->updateEntity($supervision, $data);

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
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $period = $this->periodRepository->findOneByExternalId($data->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Period "%s" on timetable supervision ID "%s" was not found.', $data->getPeriod(), $data->getId()));
        }

        $week = $this->weekRepository->findOneByKey($data->getWeek());

        if($week === null) {
            throw new ImportException(sprintf('Week "%s" on timetable supervision ID "%s" was not found.', $data->getWeek(), $data->getId()));
        }

        $teacher = $this->teacherRepository->findOneByExternalId($data->getTeacher());

        if($teacher === null) {
            throw new ImportException(sprintf('Teacher with ID "%s" on timetable supervision ID "%s" was not found.', $data->getTeacher(), $data->getId()));
        }

        $entity->setPeriod($period);
        $entity->setWeek($week);
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
}
<?php

namespace App\Import;

use App\Entity\TimetableLesson;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Utils\ArrayUtils;

class TimetableLessonsImportStrategy implements ImportStrategyInterface {

    private $timetableRepository;
    private $periodRepository;
    private $weekRepository;
    private $tuitionRepository;

    public function __construct(TimetableLessonRepositoryInterface $timetableRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableWeekRepositoryInterface $weekRepository, TuitionRepositoryInterface $tuitionRepository) {
        $this->timetableRepository = $timetableRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->tuitionRepository = $tuitionRepository;
    }

    /**
     * @return array<string, TimetableLesson>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->timetableRepository->findAll(),
            function(TimetableLesson $lesson) {
                return $lesson->getExternalId();
            }
        );
    }

    /**
     * @param TimetableLessonData $data
     * @return TimetableLesson
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $lesson = (new TimetableLesson())
            ->setExternalId($data->getId());
        $this->updateEntity($lesson, $data);

        return $lesson;
    }

    /**
     * @param TimetableLessonData $object
     * @param array<string, TimetableLesson> $existingEntities
     * @return TimetableLesson|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param TimetableLesson $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param TimetableLesson $entity
     * @param TimetableLessonData $data
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $period = $this->periodRepository->findOneByExternalId($data->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Period "%s" on timetable lesson ID "%s" was not found.', $data->getPeriod(), $data->getId()));
        }

        $tuition = $this->tuitionRepository->findOneByExternalId($data->getTuition());

        if($tuition === null) {
            throw new ImportException(sprintf('Tuition "%s" on timetable lesson ID "%s" was not found.', $data->getTuition(), $data->getId()));
        }

        $week = $this->weekRepository->findOneByKey($data->getWeek());

        if($week === null) {
            throw new ImportException(sprintf('Week "%s" on timetable lesson ID "%s" was not found.', $data->getWeek(), $data->getId()));
        }

        $entity->setPeriod($period);
        $entity->setTuition($tuition);
        $entity->setWeek($week);
        $entity->setLesson($data->getLesson());
        $entity->setIsDoubleLesson($data->isDoubleLesson());
        $entity->setDay($data->getDay());
        $entity->setRoom($data->getRoom());
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->timetableRepository->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void {
        $this->timetableRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->timetableRepository;
    }
}
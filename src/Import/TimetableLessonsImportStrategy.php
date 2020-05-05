<?php

namespace App\Import;

use App\Entity\TimetableLesson;
use App\Repository\RoomRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Utils\ArrayUtils;

class TimetableLessonsImportStrategy implements ImportStrategyInterface {

    private $timetableRepository;
    private $periodRepository;
    private $weekRepository;
    private $tuitionRepository;
    private $roomRepository;

    public function __construct(TimetableLessonRepositoryInterface $timetableRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableWeekRepositoryInterface $weekRepository, TuitionRepositoryInterface $tuitionRepository, RoomRepositoryInterface $roomRepository) {
        $this->timetableRepository = $timetableRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @param TimetableLessonsData $requestData
     * @return array<string, TimetableLesson>
     * @throws ImportException
     */
    public function getExistingEntities($requestData): array {
        $period = $this->periodRepository->findOneByExternalId($requestData->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Cannot find period with external ID "%s".', $requestData->getPeriod()));
        }

        return ArrayUtils::createArrayWithKeys(
            $this->timetableRepository->findAllByPeriod($period),
            function(TimetableLesson $lesson) {
                return $lesson->getExternalId();
            }
        );
    }

    /**
     * @param TimetableLessonData $data
     * @param TimetableLessonsData $requestData
     * @return TimetableLesson
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $lesson = (new TimetableLesson())
            ->setExternalId($data->getId());
        $this->updateEntity($lesson, $data, $requestData);

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
     * @param TimetableLessonsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $period = $this->periodRepository->findOneByExternalId($requestData->getPeriod());

        if($period === null) {
            throw new ImportException(sprintf('Period "%s" on timetable lesson ID "%s" was not found.', $requestData->getPeriod(), $data->getId()));
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

        if(!empty($data->getRoom())) {
            $entity->setRoom($this->roomRepository->findOneByExternalId($data->getRoom()));
        } else {
            $entity->setRoom(null);
        }
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

    /**
     * @param TimetableLessonsData $data
     * @return TimetableLessonData[]
     */
    public function getData($data): array {
        return $data->getLessons();
    }
}
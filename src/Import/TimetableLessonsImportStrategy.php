<?php

namespace App\Import;

use App\Entity\FreestyleTimetableLesson;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TuitionTimetableLesson;
use App\Repository\RoomRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Request\Data\TimetableLessonsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;

class TimetableLessonsImportStrategy implements ImportStrategyInterface {

    private $timetableRepository;
    private $periodRepository;
    private $weekRepository;
    private $tuitionRepository;
    private $roomRepository;
    private $teacherRepository;

    public function __construct(TimetableLessonRepositoryInterface $timetableRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableWeekRepositoryInterface $weekRepository, TuitionRepositoryInterface $tuitionRepository,
                                RoomRepositoryInterface $roomRepository, TeacherRepositoryInterface $teacherRepository) {
        $this->timetableRepository = $timetableRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->roomRepository = $roomRepository;
        $this->teacherRepository = $teacherRepository;
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
        if($data->getTuition() === null) {
            $lesson = (new FreestyleTimetableLesson());
        } else {
            $lesson = (new TuitionTimetableLesson());
        }

        $lesson->setExternalId($data->getId());
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

        if($entity instanceof TuitionTimetableLesson) {
            $tuition = $this->tuitionRepository->findOneByExternalId($data->getTuition());

            if ($tuition === null) {
                throw new ImportException(sprintf('Tuition "%s" on timetable lesson ID "%s" was not found.', $data->getTuition(), $data->getId()));
            }

            $entity->setTuition($tuition);

            if(!empty($data->getRoom())) {
                $entity->setRoom($this->roomRepository->findOneByExternalId($data->getRoom()));
            } else {
                $entity->setRoom(null);
            }
        } else if($entity instanceof FreestyleTimetableLesson) {
            $entity->setSubject($data->getSubject());
        }

        CollectionUtils::synchronize(
            $entity->getTeachers(),
            $teachers = $this->teacherRepository->findAllByExternalId($data->getTeachers()),
            function(Teacher $teacher) {
                return $teacher->getId();
            }
        );

        $week = $this->weekRepository->findOneByKey($data->getWeek());

        if($week === null) {
            throw new ImportException(sprintf('Week "%s" on timetable lesson ID "%s" was not found.', $data->getWeek(), $data->getId()));
        }

        $entity->setPeriod($period);
        $entity->setWeek($week);
        $entity->setLesson($data->getLesson());
        $entity->setIsDoubleLesson($data->isDoubleLesson());
        $entity->setDay($data->getDay());
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

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return TimetableLesson::class;
    }
}
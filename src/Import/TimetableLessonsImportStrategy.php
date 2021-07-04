<?php

namespace App\Import;

use App\Entity\Grade;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Repository\GradeRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
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
use Psr\Log\LoggerInterface;

class TimetableLessonsImportStrategy implements ImportStrategyInterface {

    private $timetableRepository;
    private $periodRepository;
    private $weekRepository;
    private $tuitionRepository;
    private $roomRepository;
    private $teacherRepository;
    private $subjectRepository;
    private $gradeRepository;

    private $logger;

    public function __construct(TimetableLessonRepositoryInterface $timetableRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableWeekRepositoryInterface $weekRepository, TuitionRepositoryInterface $tuitionRepository,
                                RoomRepositoryInterface $roomRepository, TeacherRepositoryInterface $teacherRepository,
                                SubjectRepositoryInterface $substitutionRepository, GradeRepositoryInterface $gradeRepository, LoggerInterface $logger) {
        $this->timetableRepository = $timetableRepository;
        $this->periodRepository = $periodRepository;
        $this->weekRepository = $weekRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->roomRepository = $roomRepository;
        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $substitutionRepository;
        $this->gradeRepository = $gradeRepository;
        $this->logger = $logger;
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
        $lesson = new TimetableLesson();
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

        if(!empty($data->getSubject()) && count($data->getGrades()) > 0 && count($data->getTeachers()) > 0) {
            $tuitions = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($data->getGrades(), $data->getTeachers(), $data->getSubject(), $period->getSection());

            if (count($tuitions) === 0) {
                $entity->setTuition(null);
            } else {
                if (count($tuitions) === 1) {
                    $entity->setTuition(array_shift($tuitions));
                } else {
                    $entity->setTuition(null);
                }
            }
        } else {
            $entity->setTuition(null);
        }

        if(!empty($data->getRoom())) {
            $room = $this->roomRepository->findOneByExternalId($data->getRoom());
            $entity->setRoom($room);

            if($room === null) {
                $entity->setLocation($data->getRoom());
            }
        } else {
            $entity->setRoom(null);
        }

        if($data->getSubject() !== null) {
            $subject = $this->subjectRepository->findOneByAbbreviation($data->getSubject());
            $entity->setSubject($subject);
        }

        if($entity->getTuition() === null && $entity->getSubject() === null) {
            $this->logger->info(sprintf(
                'Cannot resolve timetable lesson for subject "%s", teachers "%s" and grades "%s"',
                $data->getSubject(),
                implode(',', $data->getTeachers()),
                implode(',', $data->getGrades())
            ));
        }

        CollectionUtils::synchronize(
            $entity->getGrades(),
            $this->gradeRepository->findAllByExternalId($data->getGrades()),
            function(Grade $grade) {
                return $grade->getId();
            }
        );

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
    public function remove($entity, $requestData): bool {
        $this->timetableRepository->remove($entity);
        return true;
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
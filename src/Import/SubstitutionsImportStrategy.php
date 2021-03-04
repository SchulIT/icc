<?php

namespace App\Import;

use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Event\SubstitutionImportEvent;
use App\Repository\RoomRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\SubstitutionData;
use App\Request\Data\SubstitutionsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SubstitutionsImportStrategy implements ImportStrategyInterface, PostActionStrategyInterface {

    private $substitutionRepository;
    private $teacherRepository;
    private $studyGroupRepository;
    private $tuitionRepository;
    private $roomRepository;
    private $dispatcher;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, TeacherRepositoryInterface $teacherRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository, TuitionRepositoryInterface $tuitionRepository, RoomRepositoryInterface $roomRepository,
                                EventDispatcherInterface $eventDispatcher) {
        $this->substitutionRepository = $substitutionRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->roomRepository = $roomRepository;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param SubstitutionsData $requestData
     * @return array<string, Substitution>
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->substitutionRepository->findAll(),
            function (Substitution $substitution) {
                return $substitution->getExternalId();
            }
        );
    }

    /**
     * @param SubstitutionData $data
     * @param SubstitutionsData $requestData
     * @return Substitution
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $substitution = (new Substitution())
            ->setExternalId($data->getId());
        $this->updateEntity($substitution, $data, $requestData);

        return $substitution;
    }

    /**
     * @param SubstitutionData $object
     * @param array<string, Substitution> $existingEntities
     * @return Substitution|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Substitution $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Substitution $entity
     * @param SubstitutionData $data
     * @param SubstitutionsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $teacherIdSelector = function(Teacher $teacher) {
            return $teacher->getId();
        };

        $teachers = $this->teacherRepository->findAllByExternalId($data->getTeachers());

        if(count($teachers) !== count($data->getTeachers())) {
            $this->throwMissingTeacher($data->getTeachers(), $teachers, $data->getId());
        }

        CollectionUtils::synchronize($entity->getTeachers(), $teachers, $teacherIdSelector);

        $replacementTeachers = $this->teacherRepository->findAllByExternalId($data->getReplacementTeachers());

        if(count($replacementTeachers) !== count($data->getReplacementTeachers())) {
            $this->throwMissingTeacher($data->getReplacementTeachers(), $replacementTeachers, $data->getId());
        }

        CollectionUtils::synchronize($entity->getReplacementTeachers(), $replacementTeachers, $teacherIdSelector);

        $entity->setDate($data->getDate());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());
        $entity->setSubject($data->getSubject());
        $entity->setReplacementSubject($data->getReplacementSubject());
        $entity->setRemark($data->getText());
        $entity->setType($data->getType());
        $entity->setStartsBefore($data->startsBefore());

        if(!empty($data->getRooms())) {
            $room = $this->roomRepository->findOneByExternalId($data->getRooms());

            if($room === null) {
                $entity->setRoomName($data->getRooms());
            }

            $entity->setRoom($room);
        } else {
            $entity->setRoom(null);
        }

        if(!empty($data->getReplacementRooms())) {
            $room = $this->roomRepository->findOneByExternalId($data->getReplacementRooms());

            if($room === null) {
                $entity->setReplacementRoomName($data->getReplacementRooms());
            }

            $entity->setReplacementRoom($room);
        } else {
            $entity->setReplacementRoom(null);
        }

        $studyGroups = $this->resolveStudyGroup($data->getSubject(), $data->getGrades(), $data->getTeachers(), $data->getId());

        CollectionUtils::synchronize(
            $entity->getStudyGroups(),
            $studyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );

        $replacementStudyGroups = $this->resolveStudyGroup($data->getReplacementSubject(), $data->getReplacementGrades(), $data->getReplacementTeachers(), $data->getId());

        CollectionUtils::synchronize(
            $entity->getReplacementStudyGroups(),
            $replacementStudyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );
    }

    /**
     * @param string|null $subject
     * @param string[] $grades
     * @param string[] $teachers
     * @param string $id
     * @return StudyGroup[]
     * @throws ImportException
     */
    private function resolveStudyGroup(?string $subject, array $grades, array $teachers, string $id): array {
        $result = [ ];

        if(empty($subject)) {
            foreach($grades as $grade) {
                $studyGroup = $this->studyGroupRepository->findOneByGradeName($grade);

                if($studyGroup === null) {
                    throw new ImportException(sprintf('Study group for grade "%s" on substitution ID "%s" was not found.', $grade, $id));
                }

                $result[] = $studyGroup;
            }
        } else if(count($grades) > 0 && count($teachers) > 0) {
            $tuitions = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($grades, $teachers, $subject);

            if(count($tuitions) === 1 && $tuitions[0]->getStudyGroup() !== null) {
                $result[] = $tuitions[0]->getStudyGroup();
            } else {
                return [ ];
            }
        }

        return $result;
    }

    /**
     * @param Substitution $entity
     */
    public function persist($entity): void {
        $this->substitutionRepository->persist($entity);
    }

    /**
     * @param Substitution $entity
     */
    public function remove($entity): void {
        $this->substitutionRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->substitutionRepository;
    }

    /**
     * @param array $studyGroups
     * @param array $foundStudyGroups
     * @param string $substitutionId
     * @throws ImportException
     */
    private function throwMissingStudyGroup(array $studyGroups, array $foundStudyGroups, string $substitutionId) {
        $foundStudyGroupIds = array_map(function(StudyGroup $studyGroup) {
            return $studyGroup->getExternalId();
        }, $foundStudyGroups);

        foreach($studyGroups as $studyGroup) {
            if(!in_array($studyGroup, $foundStudyGroupIds)) {
                throw new ImportException(sprintf('Study group "%s" on substitution ID "%s" was not found.', $studyGroup, $substitutionId));
            }
        }
    }

    /**
     * @param string[] $teachers
     * @param Teacher[] $foundTeachers
     * @param string $substitutionId
     * @throws ImportException
     */
    private function throwMissingTeacher(array $teachers, array $foundTeachers, string $substitutionId) {
        $foundTeacherExternalIds = array_map(function(Teacher $teacher) {
            return $teacher->getExternalId();
        }, $foundTeachers);

        foreach($teachers as $teacher) {
            if(!in_array($teacher, $foundTeacherExternalIds)) {
                throw new ImportException(sprintf('Teacher "%s" on substitution ID "%s" was not found.', $teacher, $substitutionId));
            }
        }
    }

    /**
     * @param SubstitutionsData $data
     * @return SubstitutionData[]
     */
    public function getData($data): array {
        return $data->getSubstitutions();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Substitution::class;
    }

    public function onFinished(ImportResult $result) {
        /** @var SubstitutionsData $request */
        $request = $result->getRequest();

        if($request->isSuppressNotifications() === false) {
            $this->dispatcher->dispatch(new SubstitutionImportEvent($result->getAdded(), $result->getUpdated(), $result->getRemoved()));
        }
    }
}
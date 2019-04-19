<?php

namespace App\Import;

use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\SubstitutionData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class SubstitutionsImportStrategy implements ImportStrategyInterface {

    private $substitutionRepository;
    private $teacherRepository;
    private $studyGroupRepository;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, TeacherRepositoryInterface $teacherRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->substitutionRepository = $substitutionRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
    }

    /**
     * @return array<string, Substitution>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->substitutionRepository->findAll(),
            function (Substitution $substitution) {
                return $substitution->getExternalId();
            }
        );
    }

    /**
     * @param SubstitutionData $data
     * @return Substitution
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $substitution = (new Substitution())
            ->setExternalId($data->getId());
        $this->updateEntity($substitution, $data);

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
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        if($data->getTeacher() !== null) {
            $teacher = $this->teacherRepository->findOneByExternalId($data->getTeacher());

            if($teacher === null) {
                throw new ImportException(sprintf('Teacher with ID "%s" on substitution with ID "%s" was not found.', $data->getTeacher(), $data->getId()));
            }

            $entity->setTeacher($teacher);
        }

        if($data->getReplacementTeacher() !== null) {
            $teacher = $this->teacherRepository->findOneByExternalId($data->getReplacementTeacher());

            if($teacher === null) {
                throw new ImportException(sprintf('Replacement teacher with ID "%s" on substitution with ID "%s" was not found.', $data->getTeacher(), $data->getId()));
            }

            $entity->setReplacementTeacher($teacher);
        }

        $entity->setDate($data->getDate());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());
        $entity->setSubject($data->getSubject());
        $entity->setReplacementSubject($data->getReplacementSubject());
        $entity->setRoom($data->getRoom());
        $entity->setReplacementRoom($data->getReplacementRoom());
        $entity->setRemark($data->getRemark());
        $entity->setType($data->getType());

        $studyGroups = $this->studyGroupRepository->findAllByExternalId($data->getStudyGroups());

        if(count($studyGroups) !== count($data->getStudyGroups())) {
            $this->throwMissingStudyGroup($data->getStudyGroups(), $studyGroups, $data->getId());
        }

        CollectionUtils::synchronize(
            $entity->getStudyGroups(),
            $studyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );

        $replacementStudyGroups = $this->studyGroupRepository->findAllByExternalId($data->getReplacementStudyGroups());

        if(count($replacementStudyGroups) !== count($data->getReplacementStudyGroups())) {
            $this->throwMissingStudyGroup($data->getStudyGroups(), $replacementStudyGroups, $data->getId());
        }

        CollectionUtils::synchronize(
            $entity->getReplacementStudyGroups(),
            $replacementStudyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );
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
}
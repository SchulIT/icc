<?php

namespace App\Import;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudyGroupData;
use App\Request\Data\StudyGroupsData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class StudyGroupImportStrategy implements ImportStrategyInterface {

    private $studyGroupRepository;
    private $gradeRepository;

    public function __construct(StudyGroupRepositoryInterface $studyGroupRepository, GradeRepositoryInterface $gradeRepository) {
        $this->studyGroupRepository = $studyGroupRepository;
        $this->gradeRepository = $gradeRepository;
    }

    /**
     * @return array<string, StudyGroup>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->studyGroupRepository->findAll(),
            function(StudyGroup $studyGroup) {
                return $studyGroup->getExternalId();
            }
        );
    }

    /**
     * @param StudyGroupData $data
     * @return StudyGroup
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $studyGroup = (new StudyGroup())
            ->setExternalId($data->getId());
        $this->updateEntity($studyGroup, $data);

        return $studyGroup;
    }

    /**
     * @param StudyGroupData $object
     * @param array<string, StudyGroup> $existingEntities
     * @return StudyGroup|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param StudyGroup $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param StudyGroup $entity
     * @param StudyGroupData $data
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $entity->setName($data->getName());
        $entity->setType(new StudyGroupType($data->getType()));

        $grades = $this->gradeRepository->findAllByExternalId($data->getGrades());

        if(count($grades) !== count($data->getGrades())) {
            $this->throwGradeIsMissing($data->getGrades(), $grades, $data->getId());
        }

        CollectionUtils::synchronize(
            $entity->getGrades(),
            $grades,
            function(Grade $grade) {
                return $grade->getId();
            }
        );
    }

    /**
     * @param StudyGroup $entity
     */
    public function persist($entity): void {
        $this->studyGroupRepository->persist($entity);
    }

    /**
     * @param StudyGroup $entity
     */
    public function remove($entity): void {
        $this->studyGroupRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->studyGroupRepository;
    }

    /**
     * @param string[] $grades
     * @param Grade[] $foundGrades
     * @throws ImportException
     */
    private function throwGradeIsMissing(array $grades, array $foundGrades, string $studyGroupExternalId) {
        $foundIds = array_map(function(Grade $grade) {
            return $grade->getExternalId();
        }, $foundGrades);

        foreach($grades as $grade) {
            if(!in_array($grade, $foundIds)) {
                throw new ImportException(sprintf('Grade "%s" was not found on study group ID "%s"', $grade, $studyGroupExternalId));
            }
        }
    }

    /**
     * @param StudyGroupsData $data
     * @return StudyGroupData[]
     */
    public function getData($data): array {
        return $data->getStudyGroups();
    }
}
<?php

namespace App\Import;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\StudyGroupData;
use App\Request\Data\StudyGroupsData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class StudyGroupImportStrategy implements ImportStrategyInterface {

    public function __construct(private StudyGroupRepositoryInterface $studyGroupRepository, private GradeRepositoryInterface $gradeRepository, private SectionRepositoryInterface $sectionRepository)
    {
    }

    /**
     * @param StudyGroupsData $requestData
     * @return array<string, StudyGroup>
     * @throws SectionNotFoundException
     */
    public function getExistingEntities($requestData): array {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        return ArrayUtils::createArrayWithKeys(
            $this->studyGroupRepository->findAllBySection($section),
            fn(StudyGroup $studyGroup) => $studyGroup->getExternalId()
        );
    }

    /**
     * @param StudyGroupData $data
     * @param StudyGroupsData $requestData
     * @return StudyGroup
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $studyGroup = (new StudyGroup())
            ->setExternalId($data->getId());
        $this->updateEntity($studyGroup, $data, $requestData);

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
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param StudyGroup $entity
     * @param StudyGroupData $data
     * @param StudyGroupsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
        $entity->setType(StudyGroupType::from($data->getType()));

        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());
        $entity->setSection($section);

        $grades = $this->gradeRepository->findAllByExternalId($data->getGrades());

        if(count($grades) !== count($data->getGrades())) {
            $this->throwGradeIsMissing($data->getGrades(), $grades, $data->getId());
        }

        CollectionUtils::synchronize(
            $entity->getGrades(),
            $grades,
            fn(Grade $grade) => $grade->getId()
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
     * @param StudyGroupsData $requestData
     */
    public function remove($entity, $requestData): bool {
        $this->studyGroupRepository->remove($entity);
        return true;
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
        $foundIds = array_map(fn(Grade $grade) => $grade->getExternalId(), $foundGrades);

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

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return StudyGroup::class;
    }
}
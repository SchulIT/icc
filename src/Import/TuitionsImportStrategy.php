<?php

namespace App\Import;

use App\Entity\Section;
use App\Entity\StudyGroup;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TuitionData;
use App\Request\Data\TuitionsData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class TuitionsImportStrategy implements ImportStrategyInterface {

    private bool $isInitialized = false;

    private array $subjectCache = [ ];
    private array $teacherCache = [ ];
    private array $studyGroupCache = [ ];

    public function __construct(private TuitionRepositoryInterface $tuitionRepository, private SubjectRepositoryInterface $subjectRepository, private TeacherRepositoryInterface $teacherRepository, private StudyGroupRepositoryInterface $studyGroupRepository, private SectionRepositoryInterface $sectionRepository)
    {
    }

    /**
     * @throws SectionNotFoundException
     */
    private function initialize(TuitionsData $requestData) {
        if($this->isInitialized === true) {
            return;
        }

        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $this->subjectCache = ArrayUtils::createArrayWithKeys(
            $this->subjectRepository->findAll(),
            fn(Subject $subject) => $subject->getExternalId()
        );

        $this->teacherCache = ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAllBySection($section),
            fn(Teacher $teacher) => $teacher->getExternalId()
        );

        $this->studyGroupCache = ArrayUtils::createArrayWithKeys(
            $this->studyGroupRepository->findAllBySection($section),
            fn(StudyGroup $studyGroup) => $studyGroup->getExternalId()
        );

        $this->isInitialized = true;
    }

    /**
     * @param TuitionsData $requestData
     * @return array<string, Tuition>
     * @throws SectionNotFoundException
     */
    public function getExistingEntities($requestData): array {
        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        return ArrayUtils::createArrayWithKeys(
            $this->tuitionRepository->findAllBySection($section),
            fn(Tuition $tuition) => $tuition->getExternalId()
        );
    }

    /**
     * @param TuitionData $data
     * @param TuitionsData $requestData
     * @return Tuition
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $tuition = (new Tuition())
            ->setExternalId($data->getId());
        $this->updateEntity($tuition, $data, $requestData);

        return $tuition;
    }

    /**
     * @param TuitionData $object
     * @param array<string, Tuition> $existingEntities
     * @return Tuition|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Tuition $entity
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Tuition $entity
     * @param TuitionData $data
     * @param TuitionsData $requestData
     * @throws ImportException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $this->initialize($requestData);

        $subject = $this->subjectCache[$data->getSubject()] ?? null;

        if($subject === null) {
            throw new ImportException(sprintf('Subject "%s" was not found on tuition with ID "%s"', $data->getSubject(), $data->getId()));
        }

        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $entity->setSection($section);

        $teachers = ArrayUtils::findAllWithKeys($this->teacherCache, $data->getTeachers());

        if(count($teachers) !== count($data->getTeachers())) {
            $this->throwTeacherIsMissing($data->getTeachers(), $teachers, $data->getId());
        }

        $studyGroup = $this->studyGroupCache[$data->getStudyGroup()] ?? null;

        if($studyGroup === null) {
            throw new ImportException(sprintf('Study group with ID "%s" was not found on tuition with ID "%s"', $data->getStudyGroup(), $data->getId()));
        }

        $entity->setSubject($subject);
        $entity->setStudyGroup($studyGroup);
        $entity->setName($data->getName());
        $entity->setDisplayName($data->getDisplayName());

        CollectionUtils::synchronize(
            $entity->getTeachers(),
            $teachers,
            fn(Teacher $teacher) => $teacher->getId()
        );
    }

    /**
     * @param Tuition $entity
     */
    public function persist($entity): void {
        $this->tuitionRepository->persist($entity);
    }

    /**
     * @param Tuition $entity
     */
    public function remove($entity, $requestData): bool {
        $this->tuitionRepository->remove($entity);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->tuitionRepository;
    }

    /**
     * @param string[] $teachers
     * @param Teacher[] $foundTeachers
     * @throws ImportException
     */
    private function throwTeacherIsMissing(array $teachers, array $foundTeachers, string $tuitionExternalId) {
        $foundTeacherAcronyms = array_map(fn(Teacher $teacher) => $teacher->getExternalId(), $foundTeachers);

        foreach($teachers as $teacher) {
            if(!in_array($teachers, $foundTeacherAcronyms)) {
                throw new ImportException(sprintf('Additional teacher "%s" was not found on tuition with ID "%s"', $teacher, $tuitionExternalId));
            }
        }
    }

    /**
     * @param TuitionsData $data
     * @return TuitionData[]
     */
    public function getData($data): array {
        return $data->getTuitions();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Tuition::class;
    }
}
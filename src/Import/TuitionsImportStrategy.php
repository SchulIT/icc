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

    private $tuitionRepository;
    private $subjectRepository;
    private $teacherRepository;
    private $studyGroupRepository;
    private $sectionRepository;

    private $isInitialized = false;

    private $subjectCache = [ ];
    private $teacherCache = [ ];
    private $studyGroupCache = [ ];

    public function __construct(TuitionRepositoryInterface $tuitionRepository, SubjectRepositoryInterface $subjectRepository,
                                TeacherRepositoryInterface $teacherRepository, StudyGroupRepositoryInterface $studyGroupRepository, SectionRepositoryInterface $sectionRepository) {
        $this->tuitionRepository = $tuitionRepository;
        $this->subjectRepository = $subjectRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @param TuitionsData $requestData
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
            function(Subject $subject) {
                return $subject->getExternalId();
            }
        );

        $this->teacherCache = ArrayUtils::createArrayWithKeys(
            $this->teacherRepository->findAllBySection($section),
            function(Teacher $teacher) {
                return $teacher->getExternalId();
            }
        );

        $this->studyGroupCache = ArrayUtils::createArrayWithKeys(
            $this->studyGroupRepository->findAllBySection($section),
            function(StudyGroup $studyGroup) {
                return $studyGroup->getExternalId();
            }
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
            function(Tuition $tuition) {
                return $tuition->getExternalId();
            }
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
     * @return int
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

        if($data->getTeacher() === null) {
            $entity->setTeacher(null);
        } else {
            $teacher = $this->teacherCache[$data->getTeacher()] ?? null;

            if ($teacher === null) {
                throw new ImportException(sprintf('Teacher with ID "%s" was not found on tuition with ID "%s"', $data->getTeacher(), $data->getId()));
            }

            $entity->setTeacher($teacher);
        }

        $additionalTeachers = ArrayUtils::findAllWithKeys($this->teacherCache, $data->getAdditionalTeachers());

        if(count($additionalTeachers) !== count($data->getAdditionalTeachers())) {
            $this->throwTeacherIsMissing($data->getAdditionalTeachers(), $additionalTeachers, $data->getId());
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
            $entity->getAdditionalTeachers(),
            $additionalTeachers,
            function(Teacher $teacher) {
                return $teacher->getId();
            }
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
     * @param string $tuitionExternalId
     * @throws ImportException
     */
    private function throwTeacherIsMissing(array $teachers, array $foundTeachers, string $tuitionExternalId) {
        $foundTeacherAcronyms = array_map(function(Teacher $teacher) {
            return $teacher->getExternalId();
        }, $foundTeachers);

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
<?php

namespace App\Import;

use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TuitionData;
use App\Utils\CollectionUtils;
use App\Utils\ArrayUtils;

class TuitionsImportStrategy implements ImportStrategyInterface {

    private $tuitionRepository;
    private $subjectRepository;
    private $teacherRepository;
    private $studyGroupRepository;

    public function __construct(TuitionRepositoryInterface $tuitionRepository, SubjectRepositoryInterface $subjectRepository,
                                TeacherRepositoryInterface $teacherRepository, StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->tuitionRepository = $tuitionRepository;
        $this->subjectRepository = $subjectRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
    }

    /**
     * @return array<string, Tuition>
     */
    public function getExistingEntities(): array {
        return ArrayUtils::createArrayWithKeys(
            $this->tuitionRepository->findAll(),
            function(Tuition $tuition) {
                return $tuition->getExternalId();
            }
        );
    }

    /**
     * @param TuitionData $data
     * @return Tuition
     * @throws ImportException
     */
    public function createNewEntity($data) {
        $tuition = (new Tuition())
            ->setExternalId($data->getId());
        $this->updateEntity($tuition, $data);

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
     * @throws ImportException
     */
    public function updateEntity($entity, $data): void {
        $subject = $this->subjectRepository->findOneByAbbreviation($data->getSubject());

        if($subject === null) {
            throw new ImportException(sprintf('Subject "%s" was not found on tuition with ID "%s"', $data->getSubject(), $data->getId()));
        }

        $teacher = $this->teacherRepository->findOneByAcronym($data->getTeacher());

        if($teacher === null) {
            throw new ImportException(sprintf('Teacher "%s" was not found on tuition with ID "%s"', $data->getTeacher(), $data->getId()));
        }

        $additionalTeachers = $this->teacherRepository->findAllByAcronym($data->getAdditionalTeachers());

        if(count($additionalTeachers) !== count($data->getAdditionalTeachers())) {
            $this->throwTeacherIsMissing($data->getAdditionalTeachers(), $additionalTeachers, $data->getId());
        }

        $studyGroup = $this->studyGroupRepository->findOneByExternalId($data->getStudyGroup());

        if($studyGroup === null) {
            throw new ImportException(sprintf('Study group with ID "%s" was not found on tuition with ID "%s"', $data->getStudyGroup(), $data->getId()));
        }

        $entity->setSubject($subject);
        $entity->setTeacher($teacher);
        $entity->setStudyGroup($studyGroup);
        $entity->setName($data->getName());

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
    public function remove($entity): void {
        $this->tuitionRepository->remove($entity);
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
            return $teacher->getAcronym();
        }, $foundTeachers);

        foreach($teachers as $teacher) {
            if(!in_array($teachers, $foundTeacherAcronyms)) {
                throw new ImportException(sprintf('Additional teacher "%s" was not found on tuition with ID "%s"', $teacher, $tuitionExternalId));
            }
        }
    }
}